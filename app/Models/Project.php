<?php

declare(strict_types=1);

namespace App\Models;

use App\DataTransferObjects\SeoConfig;
use App\Enums\SchemaType;
use App\Models\Concerns\HasRoutableSlug;
use App\Models\Concerns\HasSeo;
use App\Models\Concerns\HasSitemapTag;
use App\Models\Concerns\LogsAllDirtyChanges;
use App\Models\Concerns\ResolvesRoutBindingByLocalizedSlug;
use App\Models\Concerns\Scopes\HasCurrentLocaleTranslationScope;
use Carbon\CarbonImmutable;
use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Mcamara\LaravelLocalization\Interfaces\LocalizedUrlRoutable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sitemap\Contracts\Sitemapable;
use Spatie\Sitemap\Tags\Url;
use Spatie\Tags\HasTags;
use Spatie\Translatable\HasTranslations;

/**
 * @property int $id
 * @property string $title
 * @property string $short_description
 * @property string $description
 * @property string $slug
 * @property string|null $external_link
 * @property string|null $client
 * @property Collection<int, array{url: string}> $links
 * @property bool $published
 * @property bool $featured
 * @property CarbonImmutable $created_at
 * @property CarbonImmutable $updated_at
 * @property-read string $featured_image_url
 * @property-read string $short_description_or_excerpt
 * @property-read Seo|null $seo
 */
class Project extends Model implements HasMedia, LocalizedUrlRoutable, Sitemapable {
    /** @use HasFactory<ProjectFactory> */
    use HasCurrentLocaleTranslationScope, HasFactory, HasRoutableSlug, HasSeo, HasSitemapTag, HasTags, HasTranslations, InteractsWithMedia, LogsAllDirtyChanges, ResolvesRoutBindingByLocalizedSlug;

    /** @var list<string> */
    public array $translatable = [
        'title',
        'short_description',
        'description',
        'slug',
        'external_link',
    ];

    protected function casts(): array {
        return [
            'links' => 'collection',
            'published' => 'boolean',
            'featured' => 'boolean',
        ];
    }

    public function registerMediaCollections(): void {

        $this->addMediaCollection('featured_image')
            ->singleFile()
            ->useDisk(config()->string('project.featured_image.disk'))
            ->acceptsMimeTypes(config()->array('project.featured_image.accepted_mimes'));

        $this->addMediaCollection('gallery')
            ->useDisk(config()->string('project.gallery.disk'))
            ->acceptsMimeTypes(config()->array('project.gallery.accepted_mimes'));
    }

    public function registerMediaConversions(?Media $media = null): void {
        $thumb = $this->addMediaConversion('thumb')->nonQueued();
        $thumb->width(400)->height(300);

        foreach (['featured_image', 'gallery'] as $collection) {
            $conversion = $this->addMediaConversion($collection.'_webp')
                ->performOnCollections($collection);

            $conversion
                ->format('webp')
                ->quality(config()->integer("project.{$collection}.quality"))
                ->width(config()->integer("project.{$collection}.final_width_px"))
                ->height(config()->integer("project.{$collection}.final_height_px"));

            if (config()->boolean("project.{$collection}.optimize")) {
                $conversion->optimize();
            }
        }
    }

    protected function getSitemapRoute(string $locale): string {
        return route('project.show', $this->getTranslation('slug', $locale));
    }

    protected function getSitemapPriority(): float {
        $days = $this->created_at->diffInDays(now());

        return match (true) {
            $days <= 10 => 1.0,
            $days <= 30 => 0.5,
            default => 0.3,
        };
    }

    protected function getSitemapChangeFrequency(): string {
        return $this->created_at->gt(now()->subDays(2))
            ? Url::CHANGE_FREQUENCY_DAILY
            : Url::CHANGE_FREQUENCY_MONTHLY;
    }

    protected function canAddToSitemap(): bool {
        return $this->published;
    }

    public function seoDefaults(): SeoConfig {
        return new SeoConfig(
            schema_type: SchemaType::CREATIVE_WORK,
            title: '{title} {title_separator} {site_name}',
            description: '{excerpt}',
            og_image: '{featured_image}',
            schema: [
                'name' => '{title}',
                'description' => '{excerpt}',
                'image' => '{featured_image}',
                'dateCreated' => '{created_iso}',
                'dateModified' => '{modified_iso}',
                'inLanguage' => '{locale}',
            ],
        );
    }

    /**
     * @return array<string, string>
     */
    protected function seoVariables(): array {
        return [
            'title' => $this->title,
            'excerpt' => $this->short_description_or_excerpt,
            'client' => $this->client ?? '',
            'tags' => $this->tags->pluck('name')->implode(', '),
            'featured_image' => $this->featured_image_url,
            'created_iso' => $this->created_at->toIso8601String(),
            'modified_iso' => $this->updated_at->toIso8601String(),
        ];
    }

    protected function isCrawlableByStatus(): bool {
        return $this->published;
    }

    /** @return Attribute<string, never> */
    protected function shortDescriptionOrExcerpt(): Attribute {
        return Attribute::get(fn () => $this->short_description ?? Str::limit($this->description, 160, preserveWords: true));
    }

    /** @return Attribute<string, never> */
    protected function featuredImageUrl(): Attribute {
        return Attribute::get(
            fn () => $this->getFirstMediaUrl('featured_image', 'featured_image_webp')
                ?: $this->getFirstMediaUrl('featured_image')
                ?: asset('images/fallback.jpg')
        );
    }

    /** @param Builder<self> $query */
    protected function scopeWherePublished(Builder $query): void {
        $query->where('published', true);
    }
}
