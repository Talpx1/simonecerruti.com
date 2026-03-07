<?php

declare(strict_types=1);

namespace App\Providers;

use App\Filament\Macros\Field\CapitalizeFirstCharMacro;
use App\Filament\Macros\Field\CapitalizeWordsMacro;
use App\Filament\Macros\Field\LowercaseMacro;
use App\Filament\Macros\Field\UppercaseMacro;
use App\Macros\Str\ReplacePlaceholdersMacro;
use Carbon\CarbonImmutable;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;
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
        Route::macro('localized',
            fn (
                string|bool|null $locale = null,
                string|false|null $url = null,
                array $attributes = [],
                bool $forceDefaultLocation = false
            ) => LaravelLocalization::getLocalizedURL($locale, null, [], true)
        );

        Route::macro('livewireLocalized',
            fn (string $trans_name, string $component) => Route::livewire(
                LaravelLocalization::transRoute("routes.{$trans_name}"),
                $component
            )
        );

        TextInput::configureUsing(fn (TextInput $component) => $component->telRegex('/^\+[0-9]{1,4}[0-9]*$/'));
        DateTimePicker::configureUsing(fn (DateTimePicker $component) => $component->timezone(config()->string('app.actual_timezone')));
        DatePicker::configureUsing(fn (DatePicker $component) => $component->timezone(config()->string('app.actual_timezone')));
        TextColumn::configureUsing(fn (TextColumn $column) => $column->timezone(config()->string('app.actual_timezone')));

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
