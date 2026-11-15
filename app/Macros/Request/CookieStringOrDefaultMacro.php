<?php

declare(strict_types=1);

namespace App\Macros\Request;

use App\Macros\Contracts\InstanceMacro;
use Illuminate\Http\Request;

final class CookieStringOrDefaultMacro extends InstanceMacro {
    protected static function macroable(): string {
        return Request::class;
    }

    protected static function name(): string {
        return 'cookieStringOrDefault';
    }

    public static function handle(Request $request, string $key, ?string $default = null): ?string {
        $value = $request->cookie($key);

        return \is_string($value) && $value !== '' ? $value : $default;
    }
}
