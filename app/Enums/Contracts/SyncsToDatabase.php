<?php

declare(strict_types=1);

namespace App\Enums\Contracts;

interface SyncsToDatabase {
    public static function sync(): void;
}
