<?php

declare(strict_types=1);

namespace App\Providers;

use App\Macros\Route\LivewireLocalizedMacro;
use App\Macros\Route\LocalizedUrlMacro;
use App\Macros\Route\LocalizedUrlStringMacro;
use Illuminate\Support\ServiceProvider;

final class RouteMacroServiceProvider extends ServiceProvider {
    public function register(): void {
        LocalizedUrlMacro::register();
        LocalizedUrlStringMacro::register();
        LivewireLocalizedMacro::register();
    }
}
