<?php

declare(strict_types=1);

namespace App\Macros\Route;

use App\Macros\Contracts\Macro;
use Illuminate\Support\Facades\Route;

final class LocalizedUrlStringMacro extends Macro {
    protected static function macroable(): string {
        return Route::class;
    }

    protected static function name(): string {
        return 'localizedUrlString';
    }

    /** @param array<string, mixed> $attributes */
    public static function handle(
        string|bool|null $locale = null,
        string|false|null $url = null,
        array $attributes = [],
        bool $force_default_location = false
    ): string {
        return LocalizedUrlMacro::handle($locale, $url, $attributes, $force_default_location)->__toString();
    }
}
