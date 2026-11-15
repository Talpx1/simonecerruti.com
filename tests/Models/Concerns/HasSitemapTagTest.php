<?php

declare(strict_types=1);

use App\Models\BlogArticle;
use App\Models\Project;
use Spatie\Sitemap\Tags\Url;

describe('BlogArticle sitemap tag', function () {
    it('builds localized sitemap urls for a crawlable article without throwing', function () {
        // Regression for the `news.show` route bug: this route does not exist,
        // so the old implementation threw a RouteNotFoundException here.
        $article = BlogArticle::factory()->published()->create();

        $tags = $article->toSitemapTag();

        expect($tags)->toBeArray()->not->toBeEmpty()
            ->and($tags[0])->toBeInstanceOf(Url::class);
    });

    it('points the sitemap url at the blog_article.show route', function () {
        $article = BlogArticle::factory()->published()->create();

        $tags = $article->toSitemapTag();
        $slugs = collect($article->getTranslations('slug'))->values();

        expect(collect($tags)->contains(fn (Url $url) => $slugs->contains(fn ($slug) => str_contains($url->url, (string) $slug))))->toBeTrue();
    });

    it('assigns the highest priority to a freshly published article', function () {
        $article = BlogArticle::factory()->published()->create(['published_at' => now()->subDay()]);

        $tags = $article->toSitemapTag();

        expect($tags[0]->priority)->toBe(1.0);
    });

    it('returns an empty string for an article that cannot be crawled', function () {
        $article = BlogArticle::factory()->draft()->create();

        expect($article->toSitemapTag())->toBe('');
    });
});

describe('Project sitemap tag', function () {
    it('builds localized sitemap urls without throwing', function () {
        $project = Project::factory()->published()->create();

        $tags = $project->toSitemapTag();

        expect($tags)->toBeArray()->not->toBeEmpty()
            ->and($tags[0])->toBeInstanceOf(Url::class);
    });
});
