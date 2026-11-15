<?php

declare(strict_types=1);

namespace App\Data;

final readonly class WorkCardData {
    public function __construct(
        public string $title,
        public string $kind,
        public ?string $image_url,
        public ?string $summary_html,
        public string $url,
    ) {}
}
