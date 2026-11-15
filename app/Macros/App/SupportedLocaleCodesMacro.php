<?php

declare(strict_types=1);

namespace App\Macros\App;

use App\Macros\Contracts\Macro;
use Illuminate\Support\Facades\App;

final class SupportedLocaleCodesMacro extends Macro {
    protected static function macroable(): string {
        return App::class;
    }

    protected static function name(): string {
        return 'supportedLocaleCodes';
    }

    /** @return list<string> */
    public static function handle(): array {
        return \array_map(\strval(...), \array_keys(App::supportedLocales()));
    }
}
