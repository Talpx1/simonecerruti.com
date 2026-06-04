<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

readonly class VisitSourceData {
    public function __construct(
        public string $source,
        public ?string $medium = null,
        public ?int $campaign_id = null,
        public ?string $utm_source = null,
        public ?string $utm_medium = null,
        public ?string $utm_campaign = null,
        public ?string $utm_term = null,
        public ?string $utm_content = null,
        public ?string $referrer_url = null,
        public ?string $referrer_host = null,
    ) {}
}
