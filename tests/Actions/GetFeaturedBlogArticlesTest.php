<?php

declare(strict_types=1);

use App\Actions\GetFeaturedBlogArticles;
use App\Models\BlogArticle;

describe('selection', function (): void {
    it('returns only published featured articles, newest first', function (): void {
        $older = BlogArticle::factory()->published()->featured()->create(['published_at' => now()->subDays(3)]);
        $newer = BlogArticle::factory()->published()->featured()->create(['published_at' => now()->subDay()]);
        $not_featured = BlogArticle::factory()->published()->create(['featured' => false]);
        $draft = BlogArticle::factory()->draft()->featured()->create();
        $scheduled = BlogArticle::factory()->scheduled()->featured()->create();

        $result = runWithLocale('en', fn (): mixed => GetFeaturedBlogArticles::run());

        expect($result->pluck('id')->all())->toBe([$newer->id, $older->id])
            ->and($result->contains($not_featured))->toBeFalse()
            ->and($result->contains($draft))->toBeFalse()
            ->and($result->contains($scheduled))->toBeFalse();
    });

    it('honours the limit', function (): void {
        BlogArticle::factory()->published()->featured()->count(3)->create();

        expect(runWithLocale('en', fn (): mixed => GetFeaturedBlogArticles::run(limit: 1)))->toHaveCount(1)
            ->and(runWithLocale('en', fn (): mixed => GetFeaturedBlogArticles::run()))->toHaveCount(2);
    });

    it('skips articles without a translation for the current locale', function (): void {
        $english_only = BlogArticle::factory()->published()->featured()->create(['slug' => ['en' => 'english-only']]);

        $english = runWithLocale('en', fn (): mixed => GetFeaturedBlogArticles::run());

        expect($english->pluck('id')->all())->toBe([$english_only->id])
            ->and(runWithLocale('it', fn (): mixed => GetFeaturedBlogArticles::run()))->toBeEmpty();
    });

    it('eager loads tags and media', function (): void {
        BlogArticle::factory()->published()->featured()->create();

        $article = runWithLocale('en', fn (): mixed => GetFeaturedBlogArticles::run()->first());

        expect($article->relationLoaded('tags'))->toBeTrue()
            ->and($article->relationLoaded('media'))->toBeTrue();
    });
});
