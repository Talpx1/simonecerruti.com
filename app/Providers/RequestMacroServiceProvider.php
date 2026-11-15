<?php

declare(strict_types=1);

namespace App\Providers;

use App\Macros\Request\CookieStringOrDefaultMacro;
use App\Macros\Request\QueryStringOrDefaultMacro;
use App\Macros\Request\QueryStringOrNullMacro;
use Illuminate\Support\ServiceProvider;

final class RequestMacroServiceProvider extends ServiceProvider {
    public function register(): void {
        QueryStringOrDefaultMacro::register();
        QueryStringOrNullMacro::register();
        CookieStringOrDefaultMacro::register();
    }
}
