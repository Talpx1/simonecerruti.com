<?php

declare(strict_types=1);

namespace App\Macros\Request;

use App\Macros\Concerns\Macro;
use Illuminate\Http\Request;

class CookieStringOrDefaultMacro implements Macro {
    public static function register(): array {
        return [
            'cookieStringOrDefault',
            function (string $key, ?string $default = null): ?string {
                /** @var Request $this */
                $value = $this->cookie($key);

                return is_string($value) && $value !== '' ? $value : $default;
            },
        ];
    }
}
