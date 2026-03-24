<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasRoutableSlug;
use App\Models\Concerns\HasSitemapTag;
use App\Models\Concerns\LogsAllDirtyChanges;
use App\Models\Concerns\ResolvesRoutBindingByLocalizedSlug;
use App\Models\Concerns\Scopes\HasCurrentLocaleTranslationScope;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Mcamara\LaravelLocalization\Interfaces\LocalizedUrlRoutable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sitemap\Contracts\Sitemapable;
use Spatie\Sitemap\Tags\Url;
use Spatie\Tags\HasTags;
use Spatie\Translatable\HasTranslations;

class Project extends Model implements HasMedia, LocalizedUrlRoutable, Sitemapable {
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasCurrentLocaleTranslationScope, HasFactory, HasRoutableSlug, HasSitemapTag, HasTags, HasTranslations, InteractsWithMedia, LogsAllDirtyChanges, ResolvesRoutBindingByLocalizedSlug;

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
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(300)
            ->nonQueued();

        foreach (['featured_image', 'gallery'] as $collection) {
            $conf = config()->array("project.{$collection}");

            $conversion = $this->addMediaConversion($collection.'_webp')
                ->format('webp')
                ->quality($conf['quality'])
                ->width($conf['final_width_px'])
                ->height($conf['final_height_px'])
                ->performOnCollections($collection);

            if ($conf['optimize']) {
                $conversion->optimize();
            }
        }
    }

    private function getSitemapRoute(string $locale): string {
        return route('project.show', $this->getTranslation('slug', $locale));
    }

    private function getSitemapPriority(): float {
        $days = $this->created_at->diffInDays(now());

        return match (true) {
            $days <= 10 => 1.0,
            $days <= 30 => 0.5,
            default => 0.3,
        };
    }

    private function getSitemapChangeFrequency(): string {
        return $this->created_at->gt(now()->subDays(2))
            ? Url::CHANGE_FREQUENCY_DAILY
            : Url::CHANGE_FREQUENCY_MONTHLY;
    }

    /** @return Attribute<string, never> */
    public function shortDescriptionOrExcerpt(): Attribute {
        return Attribute::get(fn () => $this->short_description ?? Str::limit($this->description, 160, preserveWords: true));
    }

    /** @return Attribute<string, never> */
    public function featuredImageUrl(): Attribute {
        return Attribute::get(
            fn () => $this->getFirstMediaUrl('featured_image', 'featured_image_webp')
                ?: $this->getFirstMediaUrl('featured_image')
                ?: asset('images/fallback.jpg')
        );
    }

    #[Scope]
    public function wherePublished(Builder $query): void {
        $query->where('published', true);
    }
}
