<?php

declare(strict_types=1);

use App\Livewire\Pages\BlogArticle\BlogArticleShow;
use App\Models\BlogArticle;

use function Pest\Livewire\livewire;

it('renders a blog article', function (): void {
    $article = BlogArticle::factory()->published()->create();

    $component = livewire(BlogArticleShow::class, ['blog_article' => $article])
        ->assertOk();

    expect($component->instance()->blog_article->id)->toBe($article->id);
});

it('renders the full SEO head, including the sitewide schema nodes', function (): void {
    $article = BlogArticle::factory()->published()->create(['title' => 'My SEO Article']);

    assertPublicPageRendersFullSeoHead(
        route('blog_article.show', $article->getTranslation('slug', app()->getLocale())),
        'My SEO Article',
        'BlogPosting',
    );
});

describe('previous and next articles', function (): void {
    it('exposes the previous and next published articles', function (): void {
        $older = BlogArticle::factory()->published()->create(['published_at' => now()->subDays(3)]);
        $current = BlogArticle::factory()->published()->create(['published_at' => now()->subDays(2)]);
        $newer = BlogArticle::factory()->published()->create(['published_at' => now()->subDay()]);

        $component = livewire(BlogArticleShow::class, ['blog_article' => $current]);

        expect($component->instance()->previous->id)->toBe($older->id)
            ->and($component->instance()->next->id)->toBe($newer->id);
    });

    it('exposes none when it is the only published article', function (): void {
        $current = BlogArticle::factory()->published()->create();

        $component = livewire(BlogArticleShow::class, ['blog_article' => $current]);

        expect($component->instance()->previous)->toBeNull()
            ->and($component->instance()->next)->toBeNull();
    });

    it('renders related articles and the navigation', function (): void {
        $article = BlogArticle::factory()->published()->create(['published_at' => now()->subDays(5)]);
        BlogArticle::factory()->published()->count(4)->create();

        livewire(BlogArticleShow::class, ['blog_article' => $article])
            ->assertSeeHtml('class="article-related"')
            ->assertSeeHtml('class="previous-next-navigation"')
            ->assertSeeHtml('previous-next-navigation-next');
    });
});

describe('author box', function (): void {
    it('renders reading time, table of contents with heading ids and author bio', function (): void {
        updateBlogAuthorBio([app()->getLocale() => 'Author bio text']);

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

    it('is hidden when the bio is empty', function (): void {
        updateBlogAuthorBio([]);
        $article = BlogArticle::factory()->published()->create();

        livewire(BlogArticleShow::class, ['blog_article' => $article])->assertDontSeeHtml('class="article-author');
    });
});

it('renders the gallery block with the gallery wrapper', function (): void {
    $html = view('rich-content.blocks.gallery', ['images' => [
        ['url' => '/a.jpg', 'caption' => 'First'],
        ['url' => '/b.jpg', 'caption' => null],
    ]])->render();

    expect($html)->toContain('class="article-gallery"')
        ->toContain('<figcaption>First</figcaption>')
        ->and(\substr_count($html, '<figure>'))->toBe(2);
});
