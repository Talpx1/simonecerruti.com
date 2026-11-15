<?php

declare(strict_types=1);

namespace App\Providers;

use App\Exceptions\MissingRoutableLocalizedRouteKeyException;
use App\Filament\Macros\Field\CapitalizeFirstCharMacro;
use App\Filament\Macros\Field\CapitalizeWordsMacro;
use App\Filament\Macros\Field\LowercaseMacro;
use App\Filament\Macros\Field\UppercaseMacro;
use App\Http\Middleware\TrackVisit;
use App\Macros\Concerns\Macro;
use App\Macros\Request\CookieStringOrDefaultMacro;
use App\Macros\Request\QueryStringOrDefaultMacro;
use App\Macros\Request\QueryStringOrNullMacro;
use App\Macros\Str\ReplacePlaceholdersMacro;
use App\View\Composers\SeoComposer;
use Carbon\CarbonImmutable;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Field;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Columns\TextColumn;
use Google\Client as GoogleClient;
use Google\Service\Drive as GoogleDrive;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Uri;
use Illuminate\Validation\Rules\Password;
use League\Flysystem\Filesystem;
use Livewire\Livewire;
use Lorisleiva\Actions\Facades\Actions;
use Masbug\Flysystem\GoogleDriveAdapter;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Mcamara\LaravelLocalization\Traits\LoadsTranslatedCachedRoutes;
use Pan\PanConfiguration;

class AppServiceProvider extends ServiceProvider {
    use LoadsTranslatedCachedRoutes;

    /**
     * Register any application services.
     */
    public function register(): void {
        if ($this->app->environment('local') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }

        // Share one TrackVisit instance between handle() and terminate() so the
        // request state captured in handle() survives into terminate(), which
        // runs after the response is sent. See the middleware's $state property.
        $this->app->singleton(TrackVisit::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {
        if ($this->app->runningInConsole()) {
            Actions::registerCommands();
        }

        Password::defaults(static fn (): Password => Password::min(12)->letters()->mixedCase()->numbers()->symbols());

        Livewire::setUpdateRoute(fn (array $handle) => Route::post('/livewire/update', $handle)
            ->middleware('web')
            ->prefix(LaravelLocalization::setLocale())
        );

        Vite::useAggressivePrefetching();
        Date::use(CarbonImmutable::class);
        Model::shouldBeStrict(! $this->app->isProduction());
        Model::automaticallyEagerLoadRelationships();
        URL::forceHttps($this->app->environment(['staging', 'production']));
        DB::prohibitDestructiveCommands($this->app->environment('production'));
        Http::preventStrayRequests($this->app->runningUnitTests());

        Storage::extend('google', $this->makeGoogleDriveFilesystem(...));

        RouteServiceProvider::loadCachedRoutesUsing(fn () => $this->loadCachedRoutes());

        App::macro('supportedLocales', fn (): array => LaravelLocalization::getSupportedLocales());

        App::macro('supportedLocaleCodes',
            /** @return list<string> */
            function (): array {
                return \array_map(\strval(...), \array_keys(App::supportedLocales()));
            });

        $localizedUri = function (
            string|bool|null $locale = null,
            string|false|null $url = null,
            array $attributes = [],
            bool $force_default_location = false
        ): Uri {
            try {
                return Uri::of((string) LaravelLocalization::getLocalizedURL($locale, $url, $attributes, $force_default_location))
                    ->withoutQuery(['missing_translations']);
            } catch (MissingRoutableLocalizedRouteKeyException) {
                return Uri::of(request()->url())->withQuery(['missing_translations' => $locale]);
            }
        };

        Route::macro('localizedUrl', $localizedUri);

        // Same as localizedUrl(), but returns the URL already cast to a string —
        // saves callers the `->__toString()` (and the @var Uri hint larastan needs).
        Route::macro('localizedUrlString',
            fn (
                string|bool|null $locale = null,
                string|false|null $url = null,
                array $attributes = [],
                bool $force_default_location = false
            ): string => $localizedUri($locale, $url, $attributes, $force_default_location)->__toString()
        );

        Route::macro('livewireLocalized',
            fn (string $trans_name, string $component) => Route::livewire(
                LaravelLocalization::transRoute("routes.{$trans_name}"),
                $component
            )
        );

        DateTimePicker::configureUsing(function (DateTimePicker $component) {
            if ($component->hasTime()) {
                $component->timezone(config()->string('app.actual_timezone'));
            }
        });
        DatePicker::configureUsing(fn (DatePicker $component) => $component->timezone(config()->string('app.actual_timezone')));
        TextColumn::configureUsing(fn (TextColumn $column) => $column->timezone(config()->string('app.actual_timezone')));
        TextEntry::configureUsing(fn (TextEntry $entry) => $entry->timezone(config()->string('app.actual_timezone')));

        collect([
            UppercaseMacro::class,
            LowercaseMacro::class,
            CapitalizeWordsMacro::class,
            CapitalizeFirstCharMacro::class,
        ])->each(function (string $macro): void {
            /** @var class-string<Macro> $macro */
            Field::macro(...$macro::register());
        });

        Str::macro(...ReplacePlaceholdersMacro::register());

        View::composer(['layouts::public.index', 'layouts.public.index'], SeoComposer::class);

        collect([
            QueryStringOrDefaultMacro::class,
            QueryStringOrNullMacro::class,
            CookieStringOrDefaultMacro::class,
        ])->each(function (string $macro): void {
            /** @var class-string<Macro> $macro */
            Request::macro(...$macro::register());
        });

        PanConfiguration::maxAnalytics(config()->integer('analytics.pan.max_analytics'));

        /** @var array<int, string> $allowed_analytics */
        $allowed_analytics = config()->array('analytics.pan.allowed_analytics');
        PanConfiguration::allowedAnalytics($allowed_analytics);
    }

    /**
     * @param  array{clientId: string, clientSecret: string, refreshToken: string, folder?: string|null}  $config
     */
    private function makeGoogleDriveFilesystem(ApplicationContract $app, array $config): FilesystemAdapter {
        $client = new GoogleClient;
        $client->setClientId($config['clientId']);
        $client->setClientSecret($config['clientSecret']);
        $client->refreshToken($config['refreshToken']);

        $adapter = new GoogleDriveAdapter(new GoogleDrive($client), $config['folder'] ?? '/');

        return new FilesystemAdapter(new Filesystem($adapter, $config), $adapter, $config);
    }
}
