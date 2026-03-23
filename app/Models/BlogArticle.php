<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BlogArticleStatuses;
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
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Mcamara\LaravelLocalization\Interfaces\LocalizedUrlRoutable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sitemap\Contracts\Sitemapable;
use Spatie\Sitemap\Tags\Url;
use Spatie\Tags\HasTags;
use Spatie\Translatable\HasTranslations;

class BlogArticle extends Model implements HasMedia, LocalizedUrlRoutable, Sitemapable {
    /** @use HasFactory<\Database\Factories\BlogArticleFactory> */
    use HasCurrentLocaleTranslationScope, HasFactory, HasRoutableSlug, HasSitemapTag, HasTags, HasTranslations, InteractsWithMedia, LogsAllDirtyChanges, ResolvesRoutBindingByLocalizedSlug;

    /** @var list<string> */
    public array $translatable = ['title', 'slug', 'summary', 'content'];

    protected function casts(): array {
        return [
            'published_at' => 'immutable_datetime',
            'featured' => 'boolean',
            'status' => BlogArticleStatuses::class,
        ];
    }

    public function registerMediaCollections(): void {
        $this->addMediaCollection('featured_image')
            ->singleFile()
            ->useDisk(config()->string('blog_article.featured_image.disk'))
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    public function registerMediaConversions(?Media $media = null): void {
        $conf = config()->array('blog_article.featured_image');

        $conversion = $this->addMediaConversion('featured_image_webp')
            ->format('webp')
            ->quality($conf['quality'])
            ->width($conf['final_width_px'])
            ->height($conf['final_height_px'])
            ->performOnCollections('featured_image');

        if ($conf['optimize']) {
            $conversion->optimize();
        }
    }

    private function canAddToSitemap(): bool {
        return $this->can_be_crawled;
    }

    private function getSitemapRoute(string $locale): string {
        return route('news.show', $this->getTranslation('slug', $locale));
    }

    private function getSitemapPriority(): float {
        if (! $this->published_at) {
            return 0.1;
        }

        $days = $this->published_at->diffInDays(now());

        return match (true) {
            $days <= 3 => 1.0,
            $days <= 7 => 0.5,
            $days <= 30 => 0.3,
            default => 0.1,
        };
    }

    private function getSitemapChangeFrequency(): string {
        if (! $this->published_at) {
            return Url::CHANGE_FREQUENCY_NEVER;
        }

        return $this->published_at->gt(now()->subDays(7))
            ? Url::CHANGE_FREQUENCY_DAILY
            : Url::CHANGE_FREQUENCY_MONTHLY;
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function author(): BelongsTo {
        return $this->belongsTo(User::class, 'author_id');
    }

    /** @return Attribute<string, never> */
    public function featuredImageUrl(): Attribute {
        return Attribute::get(
            fn () => $this->getFirstMediaUrl('featured_image', 'featured_image_webp')
                ?: asset('images/fallback.jpg')
        );
    }

    /** @return Attribute<bool, never> */
    public function canBeCrawled(): Attribute {
        return Attribute::get(fn () => $this->status->allowsCrawling() && ($this->published_at?->isNowOrPast() ?? false));
    }

    #[Scope]
    public function wherePublished(Builder $query): void {
        $query
            ->where('status', BlogArticleStatuses::PUBLISHED)
            ->where('published_at', '<=', now());
    }

    /** @return Attribute<string, never> */
    public function summaryOrExcerpt(): Attribute {
        return Attribute::get(fn () => $this->summary ?? Str::limit($this->content, 160, preserveWords: true));
    }
}
