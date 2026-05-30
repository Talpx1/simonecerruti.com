<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

/**
 * Fully-resolved SEO metadata for a single page, consumed by the public layout.
 *
 * Every section is optional: the SEO partial renders only the parts that are
 * set, so a bare `new SeoData(title: '...')` is valid and emits just a title.
 * Values are expected to be final (placeholders already resolved) — the partial
 * does no further processing beyond escaping.
 */
readonly class SeoData {
    /**
     * @param  list<array{hreflang: string, href: string}>  $alternates  hreflang alternate links
     * @param  array<string, string>  $open_graph  Open Graph tags keyed by property (e.g. 'og:title' => '...')
     * @param  array<string, string>  $twitter  Twitter Card tags keyed by name (e.g. 'twitter:card' => '...')
     * @param  list<array<string, mixed>>  $json_ld  each entry is rendered as a separate ld+json <script>
     */
    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
        public ?string $canonical = null,
        public ?string $robots = null,
        public array $alternates = [],
        public array $open_graph = [],
        public array $twitter = [],
        public array $json_ld = [],
    ) {}
}
