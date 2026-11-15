<?php

declare(strict_types=1);

use App\Models\BlogArticle;
use App\Models\Project;
use Spatie\Sitemap\Tags\Url;

describe('toSitemapTag', function (): void {
    describe('on a blog article', function (): void {
        it('builds localized sitemap urls for a crawlable article', function (): void {
            $article = BlogArticle::factory()->published()->create();

            $tags = $article->toSitemapTag();

            expect($tags)->toBeArray()->not->toBeEmpty()
                ->and($tags[0])->toBeInstanceOf(Url::class);
        });

        it('points the sitemap url at the blog_article.show route', function (): void {
            $article = BlogArticle::factory()->published()->create();

            $tags = $article->toSitemapTag();
            $slugs = collect($article->getTranslations('slug'))->values();

            expect(collect($tags)->contains(fn (Url $url): bool => $slugs->contains(fn (string $slug): bool => str_contains($url->url, $slug))))->toBeTrue();
        });

        it('assigns the highest priority to a freshly published article', function (): void {
            $article = BlogArticle::factory()->published()->create(['published_at' => now()->subDay()]);

            $tags = $article->toSitemapTag();

            expect($tags[0]->priority)->toBe(1.0);
        });

        it('returns an empty string for an article that cannot be crawled', function (): void {
            $article = BlogArticle::factory()->draft()->create();

            expect($article->toSitemapTag())->toBe('');
        });
    });

    describe('on a project', function (): void {
        it('builds localized sitemap urls', function (): void {
            $project = Project::factory()->published()->create();

            $tags = $project->toSitemapTag();

            expect($tags)->toBeArray()->not->toBeEmpty()
                ->and($tags[0])->toBeInstanceOf(Url::class);
        });
    });
});
