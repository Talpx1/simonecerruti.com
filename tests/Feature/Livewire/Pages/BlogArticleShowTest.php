<?php

declare(strict_types=1);

use App\Livewire\Pages\BlogArticle\BlogArticleShow;
use App\Models\BlogArticle;
use App\Settings\BlogSettings;
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

it('exposes no previous or next article when it is the only published one', function () {
    $current = BlogArticle::factory()->published()->create();

    $component = livewire(BlogArticleShow::class, ['blog_article' => $current]);

    expect($component->instance()->previous)->toBeNull()
        ->and($component->instance()->next)->toBeNull();
});

it('renders reading time, table of contents with heading ids and author bio', function () {
    $settings = resolve(BlogSettings::class);
    $settings->author_bio = [app()->getLocale() => 'Author bio text'];
    $settings->save();

    $article = BlogArticle::factory()->published()->create([
        'content' => '<p>Intro text.</p><h2>First Section</h2><p>Body.</p><h3>Nested Part</h3><p>More.</p>',
    ]);

    livewire(BlogArticleShow::class, ['blog_article' => $article])
        ->assertSeeHtml('<h2 id="first-section">First Section</h2>')
        ->assertSeeHtml('href="#first-section"')
        ->assertSeeHtml('href="#nested-part"')
        ->assertSee(__(':minutes min read', ['minutes' => 1]))
        ->assertSeeHtml('data-effect="lightbox"')
        ->assertSee('Author bio text');
});

it('hides the author box when the bio is empty', function () {
    $settings = resolve(BlogSettings::class);
    $settings->author_bio = [];
    $settings->save();
    $article = BlogArticle::factory()->published()->create();

    livewire(BlogArticleShow::class, ['blog_article' => $article])->assertDontSeeHtml('class="article-author');
});

it('renders the gallery block with the gallery wrapper', function () {
    $html = view('rich-content.blocks.gallery', ['images' => [
        ['url' => '/a.jpg', 'caption' => 'First'],
        ['url' => '/b.jpg', 'caption' => null],
    ]])->render();

    expect($html)->toContain('class="article-gallery"')
        ->toContain('<figcaption>First</figcaption>')
        ->and(substr_count($html, '<figure>'))->toBe(2);
});

it('renders related articles and the previous and next navigation', function () {
    $article = BlogArticle::factory()->published()->create(['published_at' => now()->subDays(5)]);
    BlogArticle::factory()->published()->count(4)->create();

    livewire(BlogArticleShow::class, ['blog_article' => $article])
        ->assertSeeHtml('class="article-related"')
        ->assertSeeHtml('class="previous-next-navigation"')
        ->assertSeeHtml('previous-next-navigation-next');
});
