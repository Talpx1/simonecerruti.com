<?php

declare(strict_types=1);

namespace App\Models;

use App\Actions\CalculateReadingTime;
use App\Data\SeoConfig;
use App\Enums\BlogArticleStatuses;
use App\Enums\SchemaType;
use App\Enums\TagTypes;
use App\Models\Concerns\HasRoutableSlug;
use App\Models\Concerns\HasSeo;
use App\Models\Concerns\HasSitemapTag;
use App\Models\Concerns\LogsAllDirtyChanges;
use App\Models\Concerns\ResolvesRoutBindingByLocalizedSlug;
use App\Models\Concerns\Scopes\HasCurrentLocaleTranslationScope;
use Carbon\CarbonImmutable;
use Database\Factories\BlogArticleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
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
 * @property string $id
 * @property string $title
 * @property string $slug
 * @property string $summary
 * @property string $content
 * @property bool $featured
 * @property string $author_id
 * @property BlogArticleStatuses $status
 * @property CarbonImmutable|null $published_at
 * @property CarbonImmutable $created_at
 * @property CarbonImmutable $updated_at
 * @property-read bool $can_be_crawled
 * @property-read string $featured_image_url
 * @property-read string $summary_or_excerpt
 * @property-read int $reading_time
 * @property-read User $author
 * @property-read Seo|null $seo
 */
#[Fillable(['title', 'slug', 'summary', 'content', 'featured', 'author_id', 'status', 'published_at'])]
final class BlogArticle extends Model implements HasMedia, LocalizedUrlRoutable, Sitemapable {
    /** @use HasFactory<BlogArticleFactory> */
    use HasCurrentLocaleTranslationScope, HasFactory, HasRoutableSlug, HasSeo, HasSitemapTag, HasTags, HasTranslations, HasUuids, InteractsWithMedia, LogsAllDirtyChanges, ResolvesRoutBindingByLocalizedSlug;

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
        $conversion = $this->addMediaConversion('featured_image_webp')
            ->performOnCollections('featured_image');

        $conversion
            ->format('webp')
            ->quality(config()->integer('blog_article.featured_image.quality'))
            ->width(config()->integer('blog_article.featured_image.final_width_px'))
            ->height(config()->integer('blog_article.featured_image.final_height_px'));

        if (config()->boolean('blog_article.featured_image.optimize')) {
            $conversion->optimize();
        }
    }

    protected function canAddToSitemap(): bool {
        return $this->can_be_crawled;
    }

    protected function getSitemapRoute(string $locale): string {
        return route('blog_article.show', $this->getTranslation('slug', $locale));
    }

    protected function getSitemapPriority(): float {
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

    protected function getSitemapChangeFrequency(): string {
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
    protected function featuredImageUrl(): Attribute {
        return Attribute::get(
            fn () => $this->getFirstMediaUrl('featured_image', 'featured_image_webp')
                ?: $this->getFirstMediaUrl('featured_image')
                ?: asset('images/fallback.jpg')
        );
    }

    /** @return Attribute<int, never> */
    protected function readingTime(): Attribute {
        return Attribute::get(fn (): int => CalculateReadingTime::run($this->content));
    }

    /** @return Attribute<bool, never> */
    protected function canBeCrawled(): Attribute {
        return Attribute::get(fn () => $this->status->allowsCrawling() && ($this->published_at?->isNowOrPast() ?? false));
    }

    public function seoDefaults(): SeoConfig {
        return new SeoConfig(
            schema_type: SchemaType::BLOG_POSTING,
            title: '{title} {title_separator} {site_name}',
            description: '{excerpt}',
            og_image: '{featured_image}',
            schema: [
                'headline' => '{title}',
                'description' => '{excerpt}',
                'image' => '{featured_image}',
                'datePublished' => '{published_iso}',
                'dateModified' => '{modified_iso}',
                'inLanguage' => '{locale}',
                'articleSection' => '{category}',
                'author' => '{author}',
            ],
        );
    }

    /**
     * @return array<string, string>
     */
    protected function seoVariables(): array {
        return [
            'title' => $this->title,
            'excerpt' => $this->summary_or_excerpt,
            'author' => $this->author->name,
            'category' => $this->tags->where('type', TagTypes::BLOG_CATEGORY->value)->pluck('name')->implode(', '),
            'tags' => $this->tags->where('type', TagTypes::TAG->value)->pluck('name')->implode(', '),
            'featured_image' => $this->featured_image_url,
            'published_date' => $this->published_at?->translatedFormat('d F Y') ?? '',
            'published_iso' => $this->published_at?->toIso8601String() ?? '',
            'modified_iso' => $this->updated_at->toIso8601String(),
        ];
    }

    protected function isCrawlableByStatus(): bool {
        return $this->can_be_crawled;
    }

    /** @param Builder<self> $query */
    protected function scopeWherePublished(Builder $query): void {
        $query
            ->where('status', BlogArticleStatuses::PUBLISHED)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /** @param Builder<self> $query */
    protected function scopeWhereCanBeCrawled(Builder $query): void {
        $crawlable_statuses = BlogArticleStatuses::collect()
            ->filter(fn (BlogArticleStatuses $case) => $case->allowsCrawling())
            ->pluck('value')
            ->toArray();

        $query
            ->whereIn('status', $crawlable_statuses)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /** @return Attribute<string, never> */
    protected function summaryOrExcerpt(): Attribute {
        return Attribute::get(fn () => $this->summary ?? Str::limit($this->content, 160, preserveWords: true));
    }

    /** @return HasMany<BlogArticleRelatable, $this> */
    public function relatables(): HasMany {
        return $this->hasMany(BlogArticleRelatable::class);
    }

    /** @return MorphToMany<Project, $this> */
    public function projects(): MorphToMany {
        return $this->morphedByMany(Project::class, 'relatable', 'blog_article_relatables', 'blog_article_id');
    }

    public function previous(): ?self {
        return $this->getAdjacentPublishedArticle(-1);
    }

    public function next(): ?self {
        return $this->getAdjacentPublishedArticle(1);
    }

    private function getAdjacentPublishedArticle(int $direction): ?self {
        if (! $this->published_at) {
            return null;
        }

        /** @var list<string> $ordered_ids */
        $ordered_ids = self::query()
            ->wherePublished()
            ->orderBy('published_at')
            ->orderBy('id')
            ->pluck('id')
            ->all();

        $position = \array_search($this->id, $ordered_ids, true);
        $total = \count($ordered_ids);

        if ($position === false || $total < 2) {
            return null;
        }

        $target_position = $position + $direction;

        if ($total === 2 && ($target_position < 0 || $target_position >= $total)) {
            return null;
        }

        return self::query()->find($ordered_ids[($target_position + $total) % $total]);
    }

    /**
     * Articles sharing the most tags with this one, excluding self.
     *
     * @return Collection<int, self>
     */
    public function relatedBlogArticles(int $limit = 3): Collection {
        $tag_ids = $this->tags->pluck('id');

        if ($tag_ids->isEmpty()) {
            return self::query()
                ->wherePublished()
                ->where('id', '!=', $this->id)
                ->with(['tags', 'media', 'author'])
                ->orderByDesc('published_at')
                ->limit($limit)
                ->get();
        }

        return self::query()
            ->wherePublished()
            ->where('id', '!=', $this->id)
            ->withCount(['tags as shared_tags_count' => fn (Builder $query) => $query->whereIn('tags.id', $tag_ids)])
            ->with(['tags', 'media', 'author'])
            ->orderByDesc('shared_tags_count')
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();
    }
}
