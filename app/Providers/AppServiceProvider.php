<?php

declare(strict_types=1);

namespace App\Providers;

use App\Exceptions\MissingRoutableLocalizedRouteKeyException;
use App\Filament\Macros\Field\CapitalizeFirstCharMacro;
use App\Filament\Macros\Field\CapitalizeWordsMacro;
use App\Filament\Macros\Field\LowercaseMacro;
use App\Filament\Macros\Field\UppercaseMacro;
use App\Macros\Str\ReplacePlaceholdersMacro;
use Carbon\CarbonImmutable;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Field;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Sleep;
use Illuminate\Support\Str;
use Illuminate\Support\Uri;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class AppServiceProvider extends ServiceProvider {
    use \Mcamara\LaravelLocalization\Traits\LoadsTranslatedCachedRoutes;

    /**
     * Register any application services.
     */
    public function register(): void {
        if ($this->app->environment('local') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {
        \Livewire\Livewire::setUpdateRoute(fn ($handle) => Route::post('/livewire/update', $handle)
            ->middleware('web')
            ->prefix(LaravelLocalization::setLocale())
        );

        Vite::useAggressivePrefetching();
        Sleep::fake();
        Date::use(CarbonImmutable::class);
        Model::shouldBeStrict();
        Model::unguard();
        Model::automaticallyEagerLoadRelationships();
        URL::forceHttps($this->app->environment(['staging', 'production']));
        DB::prohibitDestructiveCommands($this->app->environment('production'));
        Http::preventStrayRequests($this->app->runningUnitTests());

        RouteServiceProvider::loadCachedRoutesUsing(fn () => $this->loadCachedRoutes());

        App::macro('supportedLocales', fn () => LaravelLocalization::getSupportedLocales());

        Route::macro('localizedUrl',
            function (
                string|bool|null $locale = null,
                string|false|null $url = null,
                array $attributes = [],
                bool $force_default_location = false
            ) {
                try {
                    $route = Uri::of(LaravelLocalization::getLocalizedURL($locale, $url, $attributes, $force_default_location))
                        ->withoutQuery(['missing_translations']);

                    return $route;
                } catch (MissingRoutableLocalizedRouteKeyException) {
                    return Uri::of(request()->url())->withQuery(['missing_translations' => $locale]);
                }
            }
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
        ])->each(
            /** @param class-string<\App\Macros\Concerns\Macro> $macro */
            fn (string $macro) => Field::macro(...$macro::register())
        );

        Str::macro(...ReplacePlaceholdersMacro::register());
    }
}
