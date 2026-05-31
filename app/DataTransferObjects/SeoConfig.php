<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use App\Enums\RobotsDirective;
use App\Enums\SchemaType;
use App\Enums\TwitterCard;

/**
 * The SEO defaults a model declares (via HasSeo::defaultSeoConfig()). Every text
 * field is a template resolved against the model's {variables}; null OG/Twitter
 * fields fall back (og_title → title, twitter_title → og_title → title, etc.).
 * Stored per-record overrides on the Seo model take precedence over these.
 */
readonly class SeoConfig {
    /**
     * @param  array<string, string>  $schema  schema.org property => value template (e.g. 'headline' => '{title}')
     * @param  list<RobotsDirective>  $robots  default robots directives (empty = index, follow)
     */
    public function __construct(
        public SchemaType $schema_type,
        public string $title,
        public string $description,
        public ?string $og_title = null,
        public ?string $og_description = null,
        public ?string $og_image = null,
        public ?string $twitter_title = null,
        public ?string $twitter_description = null,
        public ?string $twitter_image = null,
        public TwitterCard $twitter_card = TwitterCard::SUMMARY_LARGE_IMAGE,
        public array $schema = [],
        public array $robots = [],
    ) {}
}
