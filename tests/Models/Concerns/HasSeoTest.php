<?php

declare(strict_types=1);

use App\Enums\RobotsDirective;
use App\Models\BlogArticle;
use App\Models\Project;
use App\Models\Seo;
use App\Models\SeoSetting;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

describe('toSeoData', function (): void {
    it('resolves SeoData from the model defaults', function (): void {
        $article = BlogArticle::factory()->create([
            'title' => ['en' => 'Hello World', 'it' => 'Ciao Mondo'],
            'summary' => ['en' => 'An English summary', 'it' => 'Un riassunto'],
        ]);

        $seo = runWithLocale('en', fn () => $article->toSeoData());

        expect($seo->title)->toContain('Hello World')->toContain(config('app.name'))
            ->and($seo->description)->toBe('An English summary')
            ->and($seo->canonical)->not->toBeEmpty()
            ->and($seo->robots)->toBeNull()
            ->and($seo->open_graph['og:title'])->toContain('Hello World')
            ->and($seo->open_graph['og:type'])->toBe('article')
            ->and($seo->twitter['twitter:card'])->toBe('summary_large_image')
            ->and($seo->json_ld[0]['@type'])->toBe('BlogPosting')
            ->and($seo->json_ld[0]['headline'])->toBe('Hello World')
            ->and($seo->json_ld[0])->toHaveKey('datePublished');
    });

    it('lets stored SEO overrides take precedence over the defaults', function (): void {
        $article = BlogArticle::factory()->create(['title' => ['en' => 'Original', 'it' => 'Originale']]);

        expect(runWithLocale('en', fn () => $article->toSeoData()->title))->toContain('Original')
            ->and(Seo::factory()->for($article, 'seoable')->create([
                'title' => ['en' => 'Custom SEO Title', 'it' => 'Titolo SEO'],
            ]))->not->toBeNull()
            ->and(runWithLocale('en', fn () => $article->load('seo')->toSeoData()->title))->toBe('Custom SEO Title')
            ->and(runWithLocale('it', fn () => $article->toSeoData()->title))->toBe('Titolo SEO');
    });

    it('resolves SeoData in the requested locale', function (): void {
        $article = BlogArticle::factory()->create([
            'title' => ['en' => 'Hello', 'it' => 'Ciao'],
            'summary' => ['en' => 'EN summary', 'it' => 'IT riassunto'],
        ]);

        $seo = runWithLocale('it', function () use ($article) {
            LaravelLocalization::setLocale('it');

            return $article->toSeoData();
        });

        expect($seo->title)->toContain('Ciao')
            ->and($seo->description)->toBe('IT riassunto')
            ->and($seo->open_graph['og:locale'])->toBe('it_IT');
    });

    it('resolves a CreativeWork node for a project', function (): void {
        $project = Project::factory()->published()->create([
            'title' => ['en' => 'My Project', 'it' => 'Mio Progetto'],
            'short_description' => ['en' => 'A great project', 'it' => 'Un bel progetto'],
        ]);

        $seo = runWithLocale('en', fn () => $project->toSeoData());

        expect($seo->json_ld[0]['@type'])->toBe('CreativeWork')
            ->and($seo->json_ld[0]['name'])->toBe('My Project')
            ->and($seo->description)->toBe('A great project')
            ->and($seo->json_ld[0])->toHaveKey('dateCreated');
    });

    it('includes one hreflang alternate per translated locale', function (): void {
        $article = BlogArticle::factory()->create();

        expect(collect($article->toSeoData()->alternates)->pluck('hreflang')->all())
            ->toContain('en', 'it');
    });

    it('marks a non-crawlable article as noindex', function (): void {
        $article = BlogArticle::factory()->draft()->create();

        expect($article->toSeoData()->robots)->toContain('noindex');
    });

    it('honours a stored per-locale noindex override', function (): void {
        $article = BlogArticle::factory()->create();

        Seo::factory()->for($article, 'seoable')->create([
            'robots' => ['en' => [RobotsDirective::NOINDEX->value], 'it' => []],
        ]);
        $article->load('seo');

        expect(runWithLocale('en', fn () => $article->toSeoData()->robots))->toContain('noindex');
    });

    it('does not leak a single-locale override into another locale', function (): void {
        $article = BlogArticle::factory()->create([
            'title' => ['en' => 'EN Title', 'it' => 'Titolo IT'],
        ]);

        Seo::factory()->for($article, 'seoable')->create(['title' => ['en' => 'Custom EN Override']]);
        $article->load('seo');

        $english_title = runWithLocale('en', fn () => $article->toSeoData()->title);
        $italian_title = runWithLocale('it', fn () => $article->toSeoData()->title);

        expect($english_title)->toBe('Custom EN Override')
            ->and($italian_title)->toContain('Titolo IT')
            ->and($italian_title)->not->toContain('Custom EN Override');
    });

    it('does not leak a per-locale robots override into other locales', function (): void {
        $article = BlogArticle::factory()->create();

        Seo::factory()->for($article, 'seoable')->create([
            'robots' => ['en' => [RobotsDirective::NOINDEX->value], 'it' => []],
        ]);
        $article->load('seo');

        expect(runWithLocale('en', fn () => $article->toSeoData()->robots))->toContain('noindex')
            ->and(runWithLocale('it', fn () => $article->toSeoData()->robots))->toBeNull();
    });

    it('uses the SeoSetting name for the site name and og:site_name', function (): void {
        $article = BlogArticle::factory()->create(['title' => ['en' => 'Hello', 'it' => 'Ciao']]);

        expect(runWithLocale('en', fn () => $article->toSeoData()->title))->not->toContain('Acme Studio')
            ->and(SeoSetting::factory()->create(['name' => 'Acme Studio']))->not->toBeNull();

        $seo = runWithLocale('en', fn () => $article->toSeoData());

        expect($seo->title)->toContain('Acme Studio')
            ->and($seo->open_graph['og:site_name'])->toBe('Acme Studio');
    });
});

describe('isIndexable', function (): void {
    it('is false for a non-crawlable article', function (): void {
        $article = BlogArticle::factory()->draft()->create();

        expect($article->isIndexable())->toBeFalse();
    });

    it('is true for a crawlable article', function (): void {
        $article = BlogArticle::factory()->published()->create();

        expect($article->isIndexable())->toBeTrue();
    });

    it('honours a stored per-locale noindex override', function (): void {
        $article = BlogArticle::factory()->create();

        expect($article->isIndexable('en'))->toBeTrue()
            ->and(Seo::factory()->for($article, 'seoable')->create([
                'robots' => ['en' => [RobotsDirective::NOINDEX->value], 'it' => []],
            ]))->not->toBeNull()
            ->and($article->load('seo')->isIndexable('en'))->toBeFalse()
            ->and($article->isIndexable('it'))->toBeTrue();
    });
});
