<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;

class VisitSourceData extends Data {
    public function __construct(
        public readonly string $source,
        public readonly ?string $medium = null,
        public readonly ?int $campaign_id = null,
        public readonly ?string $utm_source = null,
        public readonly ?string $utm_medium = null,
        public readonly ?string $utm_campaign = null,
        public readonly ?string $utm_term = null,
        public readonly ?string $utm_content = null,
        public readonly ?string $referrer_url = null,
        public readonly ?string $referrer_host = null,
    ) {}
}
