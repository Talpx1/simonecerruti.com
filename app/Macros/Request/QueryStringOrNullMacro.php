<?php

declare(strict_types=1);

namespace App\Macros\Request;

use App\Macros\Concerns\Macro;
use Illuminate\Http\Request;

class QueryStringOrNullMacro implements Macro {
    public static function register(): array {
        return [
            'queryStringOrNull',
            function (string $key): ?string {
                /** @var Request $this */
                return $this->queryStringOrDefault($key, null);
            },
        ];
    }
}
