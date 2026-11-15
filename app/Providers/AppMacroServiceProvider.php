<?php

declare(strict_types=1);

namespace App\Providers;

use App\Macros\App\SupportedLocaleCodesMacro;
use App\Macros\App\SupportedLocalesMacro;
use Illuminate\Support\ServiceProvider;

final class AppMacroServiceProvider extends ServiceProvider {
    public function register(): void {
        SupportedLocalesMacro::register();
        SupportedLocaleCodesMacro::register();
    }
}
