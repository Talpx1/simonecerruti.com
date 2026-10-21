<?php

declare(strict_types=1);

namespace App\Macros\Request;

use App\Macros\Concerns\Macro;

class QueryStringOrNullMacro implements Macro {
    public static function register(): array {
        return [
            'queryStringOrNull',
            function (string $key): ?string {
                $value = $this->query($key);

                return is_string($value) && $value !== '' ? $value : null;
            },
        ];
    }
}
