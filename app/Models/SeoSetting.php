<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SchemaType;
use App\Models\Concerns\LogsAllDirtyChanges;
use Carbon\CarbonImmutable;
use Database\Factories\SeoSettingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\Translatable\HasTranslations;

/**
 * Site-wide SEO identity and defaults — a single-row settings record.
 *
 * @property int $id
 * @property SchemaType $type
 * @property string|null $name
 * @property string|null $description
 * @property list<string>|null $social_profiles
 * @property string|null $default_og_image
 * @property string $title_separator
 * @property bool $website_schema
 * @property CarbonImmutable $created_at
 * @property CarbonImmutable $updated_at
 */
class SeoSetting extends Model {
    /** @use HasFactory<SeoSettingFactory> */
    use HasFactory, HasTranslations, LogsAllDirtyChanges;

    private const string CACHE_KEY = 'seo_settings';

    /** @var list<string> */
    public array $translatable = ['description'];

    /**
     * Defaults live here (mirrored from no DB defaults), so the singleton works
     * with sensible values before anything is persisted.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'type' => SchemaType::PERSON->value,
        'title_separator' => ' | ',
        'website_schema' => true,
    ];

    protected function casts(): array {
        return [
            'type' => SchemaType::class,
            'social_profiles' => 'array',
            'website_schema' => 'boolean',
        ];
    }

    /**
     * The single settings row, cached forever (invalidated on save/delete).
     * Falls back to an unsaved instance carrying the default attributes, so the
     * application has working settings before the row is created in Filament.
     */
    public static function current(): self {
        return Cache::rememberForever(self::CACHE_KEY, fn (): self => self::query()->firstOrNew());
    }

    protected static function booted(): void {
        static::saved(fn () => Cache::forget(self::CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::CACHE_KEY));
    }
}
