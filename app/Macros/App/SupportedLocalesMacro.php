<?php

declare(strict_types=1);

namespace App\Macros\App;

use App\Macros\Contracts\Macro;
use Illuminate\Support\Facades\App;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

final class SupportedLocalesMacro extends Macro {
    protected static function macroable(): string {
        return App::class;
    }

    protected static function name(): string {
        return 'supportedLocales';
    }

    /** @return array<array-key, mixed> */
    public static function handle(): array {
        return LaravelLocalization::getSupportedLocales();
    }
}
