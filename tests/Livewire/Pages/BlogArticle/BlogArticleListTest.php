<?php

declare(strict_types=1);

use App\Enums\BlogCategories;
use App\Enums\TagTypes;
use App\Livewire\Pages\BlogArticle\BlogArticleList;
use App\Models\BlogArticle;
use App\Models\Tag;

use function Pest\Livewire\livewire;

describe('articles', function (): void {
    it('paginates published articles with numbered pages', function (): void {
        BlogArticle::factory()->published()->count(8)->create();

        $component = livewire(BlogArticleList::class);

        expect($component->instance()->articles())->toHaveCount(6)
            ->and($component->instance()->articles()->lastPage())->toBe(2);

        $component->call('gotoPage', 2);

        expect($component->instance()->articles())->toHaveCount(2);
    });

    it('excludes featured articles from the list', function (): void {
        $featured = BlogArticle::factory()->published()->featured()->create();
        $regular = BlogArticle::factory()->published()->create();

        $ids = livewire(BlogArticleList::class)->instance()->articles()->pluck('id');

        expect($ids)->toContain($regular->id)
            ->and($ids)->not->toContain($featured->id);
    });

    it('excludes unpublished articles from the list', function (): void {
        $draft = BlogArticle::factory()->draft()->create();
        $published = BlogArticle::factory()->published()->create();

        $ids = livewire(BlogArticleList::class)->instance()->articles()->pluck('id');

        expect($ids)->toContain($published->id)
            ->and($ids)->not->toContain($draft->id);
    });
});

describe('featured articles', function (): void {
    it('exposes the latest two featured articles', function (): void {
        $older = BlogArticle::factory()->published()->featured()->create(['published_at' => now()->subDays(3)]);
        $newer = BlogArticle::factory()->published()->featured()->create(['published_at' => now()->subDay()]);
        BlogArticle::factory()->published()->featured()->create(['published_at' => now()->subDays(10)]);

        $ids = livewire(BlogArticleList::class)->instance()->featured()->pluck('id');

        expect($ids->all())->toBe([$newer->id, $older->id]);
    });
});

describe('categories', function (): void {
    it('lists the blog category tags', function (): void {
        expect(livewire(BlogArticleList::class)->instance()->categories())->toBeEmpty();

        BlogCategories::sync();

        expect(livewire(BlogArticleList::class)->instance()->categories())->toHaveCount(2);
    });

    it('filters articles by category without hiding the featured ones', function (): void {
        $tag = Tag::findOrCreate('Practical', TagTypes::BLOG_CATEGORY->value);
        $featured = BlogArticle::factory()->published()->featured()->create();

        $tagged = BlogArticle::factory()->published()->create();
        $tagged->attachTag($tag);
        $untagged = BlogArticle::factory()->published()->create();

        $component = livewire(BlogArticleList::class);

        expect($component->instance()->articles()->pluck('id'))->toContain($tagged->id, $untagged->id);

        $component->call('filterByCategory', $tag->slug);

        $ids = $component->instance()->articles()->pluck('id');

        expect($ids)->toContain($tagged->id)
            ->and($ids)->not->toContain($untagged->id)
            ->and($component->instance()->featured()->pluck('id'))->toContain($featured->id);
    });

    it('goes back to the first page when the category changes', function (): void {
        $tag = Tag::findOrCreate('Practical', TagTypes::BLOG_CATEGORY->value);
        BlogArticle::factory()->published()->count(8)->create();

        $component = livewire(BlogArticleList::class)->call('gotoPage', 2);

        expect($component->get('paginators.page'))->toBe(2);

        $component->call('filterByCategory', $tag->slug);

        expect($component->get('paginators.page'))->toBe(1);
    });

    it('marks the active category filter', function (): void {
        BlogCategories::sync();
        $category = Tag::query()->where('type', TagTypes::BLOG_CATEGORY)->firstOrFail();

        livewire(BlogArticleList::class)
            ->assertSeeHtml('wire:click="filterByCategory(null)" class="is-active"')
            ->call('filterByCategory', $category->slug)
            ->assertDontSeeHtml('wire:click="filterByCategory(null)" class="is-active"');
    });
});

describe('rendering', function (): void {
    it('renders featured articles, rows and the numbered pager', function (): void {
        BlogArticle::factory()->published()->featured()->create(['title' => 'Featured One']);
        BlogArticle::factory()->published()->count(8)->create();

        livewire(BlogArticleList::class)
            ->assertSee('Featured One')
            ->assertSeeHtml('class="blog-featured"')
            ->assertSeeHtml('class="blog-list-item"')
            ->assertSeeHtml('class="blog-pager"')
            ->assertSeeHtml('aria-current="page"')
            ->assertSeeHtml("wire:click.prevent=\"gotoPage(2, 'page')\"");
    });

    it('renders the empty state when there are no articles', function (): void {
        livewire(BlogArticleList::class)
            ->assertSeeHtml('class="blog-empty"')
            ->assertDontSeeHtml('class="blog-pager"')
            ->assertDontSeeHtml('class="blog-featured"');
    });
});
