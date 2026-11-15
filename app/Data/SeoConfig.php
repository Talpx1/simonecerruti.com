<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\RobotsDirective;
use App\Enums\SchemaType;
use App\Enums\TwitterCard;
use Spatie\LaravelData\Data;

/**
 * The SEO defaults a model declares (via HasSeo::defaultSeoConfig()). Every text
 * field is a template resolved against the model's {variables}; null OG/Twitter
 * fields fall back (og_title → title, twitter_title → og_title → title, etc.).
 * Stored per-record overrides on the Seo model take precedence over these.
 */
class SeoConfig extends Data {
    /**
     * @param  array<string, string>  $schema  schema.org property => value template (e.g. 'headline' => '{title}')
     * @param  list<RobotsDirective>  $robots  default robots directives (empty = index, follow)
     */
    public function __construct(
        public readonly SchemaType $schema_type,
        public readonly string $title,
        public readonly string $description,
        public readonly ?string $og_title = null,
        public readonly ?string $og_description = null,
        public readonly ?string $og_image = null,
        public readonly ?string $twitter_title = null,
        public readonly ?string $twitter_description = null,
        public readonly ?string $twitter_image = null,
        public readonly TwitterCard $twitter_card = TwitterCard::SUMMARY_LARGE_IMAGE,
        public readonly array $schema = [],
        public readonly array $robots = [],
    ) {}
}
