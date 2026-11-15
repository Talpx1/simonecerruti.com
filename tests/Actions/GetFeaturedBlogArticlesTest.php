<?php

declare(strict_types=1);

use App\Actions\GetFeaturedBlogArticles;
use App\Models\BlogArticle;

describe('selection', function () {
    it('returns only published featured articles, newest first', function () {
        $older = BlogArticle::factory()->published()->create(['featured' => true, 'published_at' => now()->subDays(3)]);
        $newer = BlogArticle::factory()->published()->create(['featured' => true, 'published_at' => now()->subDay()]);
        $not_featured = BlogArticle::factory()->published()->create(['featured' => false]);
        $draft = BlogArticle::factory()->draft()->create(['featured' => true]);
        $scheduled = BlogArticle::factory()->scheduled()->create(['featured' => true]);

        $result = withLocale('en', fn () => GetFeaturedBlogArticles::run());

        expect($result->pluck('id')->all())->toBe([$newer->id, $older->id])
            ->and($result->contains($not_featured))->toBeFalse()
            ->and($result->contains($draft))->toBeFalse()
            ->and($result->contains($scheduled))->toBeFalse();
    });

    it('honours the limit', function () {
        BlogArticle::factory()->published()->count(3)->create(['featured' => true]);

        expect(withLocale('en', fn () => GetFeaturedBlogArticles::run(limit: 1)))->toHaveCount(1)
            ->and(withLocale('en', fn () => GetFeaturedBlogArticles::run()))->toHaveCount(2);
    });

    it('skips articles without a translation for the current locale', function () {
        $english_only = BlogArticle::factory()->published()->create([
            'featured' => true,
            'slug' => ['en' => 'english-only'],
        ]);

        $english = withLocale('en', fn () => GetFeaturedBlogArticles::run());

        expect($english->pluck('id')->all())->toBe([$english_only->id])
            ->and(withLocale('it', fn () => GetFeaturedBlogArticles::run()))->toBeEmpty();
    });

    it('eager loads tags and media', function () {
        BlogArticle::factory()->published()->create(['featured' => true]);

        $article = withLocale('en', fn () => GetFeaturedBlogArticles::run()->first());

        expect($article->relationLoaded('tags'))->toBeTrue()
            ->and($article->relationLoaded('media'))->toBeTrue();
    });
});
