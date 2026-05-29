<?php

declare(strict_types=1);

namespace App\Macros\Request;

use App\Macros\Concerns\Macro;
use Illuminate\Http\Request;

class QueryStringOrDefaultMacro implements Macro {
    public static function register(): array {
        return [
            'queryStringOrDefault',
            function (string $key, ?string $default = null): ?string {
                /** @var Request $this */
                $value = $this->query($key);

                return is_string($value) && $value !== '' ? $value : $default;
            },
        ];
    }
}
