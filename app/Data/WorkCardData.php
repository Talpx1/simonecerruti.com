<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;

final class WorkCardData extends Data {
    public function __construct(
        public readonly string $title,
        public readonly string $kind,
        public readonly ?string $image_url,
        public readonly ?string $summary_html,
        public readonly string $url,
    ) {}
}
