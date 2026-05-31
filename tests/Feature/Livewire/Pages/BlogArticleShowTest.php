<?php

declare(strict_types=1);

use App\Livewire\Pages\BlogArticle\BlogArticleShow;
use App\Models\BlogArticle;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;

use function Pest\Livewire\livewire;

beforeEach(fn () => $this->withoutVite());

it('renders a blog article', function () {
    $article = BlogArticle::factory()->published()->create();

    $component = livewire(BlogArticleShow::class, ['blog_article' => $article])
        ->assertOk();

    expect($component->instance()->blog_article->id)->toBe($article->id);
});

it('renders the full SEO head, including the sitewide schema nodes', function () {
    $article = BlogArticle::factory()->published()->create(['title' => 'My SEO Article']);
    $url = route('blog_article.show', $article->getTranslation('slug', app()->getLocale()));

    $html = $this->withoutMiddleware([
        LaravelLocalizationRedirectFilter::class,
        LocaleSessionRedirect::class,
    ])->get($url)->assertOk()->getContent();

    expect($html)
        ->toContain('<title>My SEO Article | '.config('app.name').'</title>')
        ->toContain('<meta name="description"')
        ->toContain('<link rel="canonical"')
        ->toContain('property="og:title"')
        ->toContain('name="twitter:card"')
        // The page node and the prepended sitewide nodes share one @graph.
        ->toContain('"@type":"BlogPosting"')
        ->toContain('"@type":"WebSite"')
        ->and(substr_count((string) $html, '<title'))->toBe(1)
        ->and(substr_count((string) $html, 'application/ld+json'))->toBe(1);
});

it('exposes previous and next published articles', function () {
    $older = BlogArticle::factory()->published()->create(['published_at' => now()->subDays(3)]);
    $current = BlogArticle::factory()->published()->create(['published_at' => now()->subDays(2)]);
    $newer = BlogArticle::factory()->published()->create(['published_at' => now()->subDay()]);

    $component = livewire(BlogArticleShow::class, ['blog_article' => $current]);

    expect($component->instance()->previous->id)->toBe($older->id)
        ->and($component->instance()->next->id)->toBe($newer->id);
});
