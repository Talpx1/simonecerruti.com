<?php

declare(strict_types=1);

namespace App\Data;

final readonly class TableOfContentsEntryData {
    public function __construct(
        public string $id,
        public string $title,
        public int $level,
    ) {}
}
