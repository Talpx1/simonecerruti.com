<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;

/**
 * Fully-resolved SEO metadata for a single page, consumed by the public layout.
 *
 * Every section is optional: the SEO partial renders only the parts that are
 * set, so a bare `new SeoData(title: '...')` is valid and emits just a title.
 * Values are expected to be final (placeholders already resolved) — the partial
 * does no further processing beyond escaping.
 */
class SeoData extends Data {
    /**
     * @param  list<array{hreflang: string, href: string}>  $alternates  hreflang alternate links
     * @param  array<string, string>  $open_graph  Open Graph tags keyed by property (e.g. 'og:title' => '...')
     * @param  array<string, string>  $twitter  Twitter Card tags keyed by name (e.g. 'twitter:card' => '...')
     * @param  list<array<string, mixed>>  $json_ld  each entry is rendered as a separate ld+json <script>
     */
    public function __construct(
        public readonly ?string $title = null,
        public readonly ?string $description = null,
        public readonly ?string $canonical = null,
        public readonly ?string $robots = null,
        public readonly array $alternates = [],
        public readonly array $open_graph = [],
        public readonly array $twitter = [],
        public readonly array $json_ld = [],
    ) {}

    /**
     * A copy with the given schema.org nodes prepended to the JSON-LD @graph,
     * used to add sitewide nodes (WebSite, site identity) ahead of the page node.
     *
     * @param  list<array<string, mixed>>  $nodes
     */
    public function prependJsonLd(array $nodes): self {
        return new self(
            title: $this->title,
            description: $this->description,
            canonical: $this->canonical,
            robots: $this->robots,
            alternates: $this->alternates,
            open_graph: $this->open_graph,
            twitter: $this->twitter,
            json_ld: [...$nodes, ...$this->json_ld],
        );
    }
}
