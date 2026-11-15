<?php

declare(strict_types=1);

namespace App\Providers;

use App\Enums\Abilities;
use App\Http\Middleware\TrackVisit;
use App\Models\User;
use App\View\Composers\SeoComposer;
use Carbon\CarbonImmutable;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Columns\TextColumn;
use Google\Client as GoogleClient;
use Google\Service\Drive as GoogleDrive;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
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

        Gate::define(Abilities::GENERATE_AI_CONTENT->value, $this->canAccessAdminPanel(...));
        Gate::define(Abilities::MANAGE_AI_SETTINGS->value, $this->canAccessAdminPanel(...));

        RouteServiceProvider::loadCachedRoutesUsing(fn () => $this->loadCachedRoutes());

        DateTimePicker::configureUsing(function (DateTimePicker $component) {
            if ($component->hasTime()) {
                $component->timezone(config()->string('app.actual_timezone'));
            }
        });
        DatePicker::configureUsing(fn (DatePicker $component) => $component->timezone(config()->string('app.actual_timezone')));
        TextColumn::configureUsing(fn (TextColumn $column) => $column->timezone(config()->string('app.actual_timezone')));
        TextEntry::configureUsing(fn (TextEntry $entry) => $entry->timezone(config()->string('app.actual_timezone')));

        View::composer(['layouts::public.index', 'layouts.public.index'], SeoComposer::class);

        PanConfiguration::maxAnalytics(config()->integer('analytics.pan.max_analytics'));

        /** @var array<int, string> $allowed_analytics */
        $allowed_analytics = config()->array('analytics.pan.allowed_analytics');
        PanConfiguration::allowedAnalytics($allowed_analytics);
    }

    private function canAccessAdminPanel(User $user): bool {
        return $user->canAccessPanel(Filament::getDefaultPanel());
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
