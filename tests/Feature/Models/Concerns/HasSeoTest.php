<?php

declare(strict_types=1);

use App\Enums\RobotsDirective;
use App\Models\BlogArticle;
use App\Models\Project;
use App\Models\Seo;
use App\Models\SeoSetting;

it('resolves SeoData from the model defaults', function () {
    $article = BlogArticle::factory()->create([
        'title' => ['en' => 'Hello World', 'it' => 'Ciao Mondo'],
        'summary' => ['en' => 'An English summary', 'it' => 'Un riassunto'],
    ]);

    $seo = withLocale('en', fn () => $article->toSeoData());

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

it('lets stored SEO overrides take precedence over the defaults', function () {
    $article = BlogArticle::factory()->create(['title' => ['en' => 'Original', 'it' => 'Originale']]);

    Seo::factory()->for($article, 'seoable')->create([
        'title' => ['en' => 'Custom SEO Title', 'it' => 'Titolo SEO'],
    ]);
    $article->load('seo');

    expect(withLocale('en', fn () => $article->toSeoData()->title))->toBe('Custom SEO Title')
        ->and(withLocale('it', fn () => $article->toSeoData()->title))->toBe('Titolo SEO');
});

it('resolves SeoData in the requested locale', function () {
    $article = BlogArticle::factory()->create([
        'title' => ['en' => 'Hello', 'it' => 'Ciao'],
        'summary' => ['en' => 'EN summary', 'it' => 'IT riassunto'],
    ]);

    $seo = withLocale('it', fn () => $article->toSeoData());

    expect($seo->title)->toContain('Ciao')
        ->and($seo->description)->toBe('IT riassunto')
        ->and($seo->open_graph['og:locale'])->toBe('it_IT');
});

it('resolves a CreativeWork node for a project', function () {
    $project = Project::factory()->create([
        'title' => ['en' => 'My Project', 'it' => 'Mio Progetto'],
        'short_description' => ['en' => 'A great project', 'it' => 'Un bel progetto'],
        'published' => true,
    ]);

    $seo = withLocale('en', fn () => $project->toSeoData());

    expect($seo->json_ld[0]['@type'])->toBe('CreativeWork')
        ->and($seo->json_ld[0]['name'])->toBe('My Project')
        ->and($seo->description)->toBe('A great project')
        ->and($seo->json_ld[0])->toHaveKey('dateCreated');
});

it('includes one hreflang alternate per translated locale', function () {
    $article = BlogArticle::factory()->create();

    expect(collect($article->toSeoData()->alternates)->pluck('hreflang')->all())
        ->toContain('en', 'it');
});

it('marks a non-crawlable article as noindex', function () {
    $article = BlogArticle::factory()->draft()->create();

    expect($article->toSeoData()->robots)->toContain('noindex')
        ->and($article->isIndexable())->toBeFalse();
});

it('honours a stored per-locale noindex override', function () {
    $article = BlogArticle::factory()->create();

    Seo::factory()->for($article, 'seoable')->create([
        'robots' => ['en' => [RobotsDirective::NOINDEX->value], 'it' => []],
    ]);
    $article->load('seo');

    expect(withLocale('en', fn () => $article->toSeoData()->robots))->toContain('noindex')
        ->and($article->isIndexable('en'))->toBeFalse()
        ->and($article->isIndexable('it'))->toBeTrue();
});

it('does not leak a single-locale override into another locale', function () {
    $article = BlogArticle::factory()->create([
        'title' => ['en' => 'EN Title', 'it' => 'Titolo IT'],
    ]);

    // Override stored only for EN.
    Seo::factory()->for($article, 'seoable')->create(['title' => ['en' => 'Custom EN Override']]);
    $article->load('seo');

    $en = withLocale('en', fn () => $article->toSeoData()->title);
    $it = withLocale('it', fn () => $article->toSeoData()->title);

    expect($en)->toBe('Custom EN Override')
        // IT has no override → it falls through to the model's default template,
        // NOT the EN override (no spatie fallback leak).
        ->and($it)->toContain('Titolo IT')
        ->and($it)->not->toContain('Custom EN Override');
});

it('honours a per-locale robots override without leaking to other locales', function () {
    $article = BlogArticle::factory()->create();

    Seo::factory()->for($article, 'seoable')->create([
        'robots' => ['en' => [RobotsDirective::NOINDEX->value], 'it' => []],
    ]);
    $article->load('seo');

    expect(withLocale('en', fn () => $article->toSeoData()->robots))->toContain('noindex')
        ->and(withLocale('it', fn () => $article->toSeoData()->robots))->toBeNull();
});

it('uses the SeoSetting name for {site_name} and og:site_name', function () {
    SeoSetting::factory()->create(['name' => 'Acme Studio']);

    $article = BlogArticle::factory()->create(['title' => ['en' => 'Hello', 'it' => 'Ciao']]);
    $seo = withLocale('en', fn () => $article->toSeoData());

    expect($seo->title)->toContain('Acme Studio')
        ->and($seo->open_graph['og:site_name'])->toBe('Acme Studio');
});
