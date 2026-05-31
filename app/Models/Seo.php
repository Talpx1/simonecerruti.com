<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SchemaType;
use App\Enums\TwitterCard;
use App\Models\Concerns\LogsAllDirtyChanges;
use Carbon\CarbonImmutable;
use Database\Factories\SeoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Translatable\HasTranslations;

/**
 * Per-record SEO overrides. Every column is nullable: a null value means
 * "fall back to the model's default" (resolved by the HasSeo trait in Fase 3).
 *
 * @property int $id
 * @property string $seoable_type
 * @property int $seoable_id
 * @property string|null $title
 * @property string|null $description
 * @property string|null $og_title
 * @property string|null $og_description
 * @property string|null $og_image
 * @property string|null $twitter_title
 * @property string|null $twitter_description
 * @property string|null $twitter_image
 * @property string|null $canonical
 * @property list<string>|null $robots
 * @property array<string, mixed>|null $schema_overrides
 * @property SchemaType|null $schema_type
 * @property TwitterCard|null $twitter_card
 * @property CarbonImmutable $created_at
 * @property CarbonImmutable $updated_at
 */
class Seo extends Model {
    /** @use HasFactory<SeoFactory> */
    use HasFactory, HasTranslations, LogsAllDirtyChanges;

    protected $table = 'seo';

    /** @var list<string> */
    public array $translatable = [
        'title',
        'description',
        'og_title',
        'og_description',
        'og_image',
        'twitter_title',
        'twitter_description',
        'twitter_image',
        'canonical',
        'robots',
        'schema_overrides',
    ];

    protected function casts(): array {
        return [
            'schema_type' => SchemaType::class,
            'twitter_card' => TwitterCard::class,
            'robots' => 'array',
            'schema_overrides' => 'array',
        ];
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function seoable(): MorphTo {
        return $this->morphTo();
    }
}
