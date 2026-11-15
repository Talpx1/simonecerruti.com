<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;

final class TableOfContentsEntryData extends Data {
    public function __construct(
        public readonly string $id,
        public readonly string $title,
        public readonly int $level,
    ) {}
}
