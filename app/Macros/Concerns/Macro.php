<?php

declare(strict_types=1);

namespace App\Macros\Concerns;

interface Macro {
    /** @return array{0: string, 1: callable} */
    public static function register(): array;
}
