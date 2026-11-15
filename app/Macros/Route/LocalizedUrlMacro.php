<?php

declare(strict_types=1);

namespace App\Macros\Route;

use App\Exceptions\MissingRoutableLocalizedRouteKeyException;
use App\Macros\Contracts\Macro;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Uri;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

final class LocalizedUrlMacro extends Macro {
    protected static function macroable(): string {
        return Route::class;
    }

    protected static function name(): string {
        return 'localizedUrl';
    }

    /** @param array<string, mixed> $attributes */
    public static function handle(
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
    }
}
