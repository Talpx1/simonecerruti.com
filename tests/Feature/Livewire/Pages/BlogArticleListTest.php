<?php

declare(strict_types=1);

use App\Enums\BlogCategories;
use App\Enums\TagTypes;
use App\Livewire\Pages\BlogArticle\BlogArticleList;
use App\Models\BlogArticle;
use App\Models\Tag;

use function Pest\Livewire\livewire;

beforeEach(fn () => $this->withoutVite());

it('paginates published articles with numbered pages', function () {
    BlogArticle::factory()->published()->count(8)->create();

    $component = livewire(BlogArticleList::class);

    expect($component->instance()->articles())->toHaveCount(6)
        ->and($component->instance()->articles()->lastPage())->toBe(2);

    $component->call('gotoPage', 2);

    expect($component->instance()->articles())->toHaveCount(2);
});

it('lists the blog category tags', function () {
    BlogCategories::sync();

    expect(livewire(BlogArticleList::class)->instance()->categories())->toHaveCount(2);
});

it('exposes the latest two featured articles', function () {
    $older = BlogArticle::factory()->published()->featured()->create(['published_at' => now()->subDays(3)]);
    $newer = BlogArticle::factory()->published()->featured()->create(['published_at' => now()->subDay()]);
    BlogArticle::factory()->published()->featured()->create(['published_at' => now()->subDays(10)]);

    $ids = livewire(BlogArticleList::class)->instance()->featured()->pluck('id');

    expect($ids->all())->toBe([$newer->id, $older->id]);
});

it('excludes featured articles from the list', function () {
    $featured = BlogArticle::factory()->published()->featured()->create();
    $regular = BlogArticle::factory()->published()->create();

    $ids = livewire(BlogArticleList::class)->instance()->articles()->pluck('id');

    expect($ids)->toContain($regular->id)
        ->and($ids)->not->toContain($featured->id);
});

it('filters articles by category without hiding the featured ones', function () {
    $tag = Tag::findOrCreate('Practical', TagTypes::BLOG_CATEGORY->value);
    $featured = BlogArticle::factory()->published()->featured()->create();

    $tagged = BlogArticle::factory()->published()->create();
    $tagged->attachTag($tag);
    $untagged = BlogArticle::factory()->published()->create();

    $component = livewire(BlogArticleList::class);
    $component->call('filterByCategory', $tag->slug);

    $ids = $component->instance()->articles()->pluck('id');

    expect($ids)->toContain($tagged->id)
        ->and($ids)->not->toContain($untagged->id)
        ->and($component->instance()->featured()->pluck('id'))->toContain($featured->id);
});

it('goes back to the first page when the category changes', function () {
    $tag = Tag::findOrCreate('Practical', TagTypes::BLOG_CATEGORY->value);
    BlogArticle::factory()->published()->count(8)->create();

    $component = livewire(BlogArticleList::class)->call('gotoPage', 2);
    $component->call('filterByCategory', $tag->slug);

    expect($component->get('paginators.page'))->toBe(1);
});

it('renders featured articles, rows and the numbered pager', function () {
    BlogArticle::factory()->published()->featured()->create(['title' => 'Featured One']);
    BlogArticle::factory()->published()->count(8)->create();

    livewire(BlogArticleList::class)
        ->assertSee('Featured One')
        ->assertSeeHtml('class="bfeats"')
        ->assertSeeHtml('class="prow"')
        ->assertSeeHtml('class="pager"')
        ->assertSeeHtml('aria-current="page"')
        ->assertSeeHtml("wire:click.prevent=\"gotoPage(2, 'page')\"");
});

it('renders the empty state when there are no articles', function () {
    livewire(BlogArticleList::class)
        ->assertSeeHtml('class="empty"')
        ->assertDontSeeHtml('class="pager"')
        ->assertDontSeeHtml('class="bfeats"');
});

it('marks the active category filter', function () {
    BlogCategories::sync();
    $category = Tag::query()->where('type', TagTypes::BLOG_CATEGORY->value)->firstOrFail();

    livewire(BlogArticleList::class)
        ->assertSeeHtml('wire:click="filterByCategory(null)" class="on"')
        ->call('filterByCategory', $category->slug)
        ->assertDontSeeHtml('wire:click="filterByCategory(null)" class="on"');
});
