<?php

declare(strict_types=1);

use App\Enums\BlogCategories;
use App\Enums\TagTypes;
use App\Livewire\Pages\BlogArticle\BlogArticleList;
use App\Models\BlogArticle;
use Spatie\Tags\Tag;

use function Pest\Livewire\livewire;

beforeEach(fn () => $this->withoutVite());

it('paginates published articles with load-more', function () {
    BlogArticle::factory()->published()->count(8)->create();

    $component = livewire(BlogArticleList::class);

    expect($component->instance()->getLoadMoreData('articles'))->toHaveCount(6)
        ->and($component->instance()->canLoadMore('articles'))->toBeTrue();

    $component->call('loadMore', 'articles');

    expect($component->instance()->canLoadMore('articles'))->toBeFalse();
});

it('lists the blog category tags', function () {
    BlogCategories::sync();

    expect(livewire(BlogArticleList::class)->instance()->categories())->toHaveCount(2);
});

it('exposes the featured article when no category is active', function () {
    $featured = BlogArticle::factory()->published()->featured()->create();
    BlogArticle::factory()->published()->count(2)->create();

    expect(livewire(BlogArticleList::class)->instance()->featured()->id)->toBe($featured->id);
});

it('filters articles by category', function () {
    $tag = Tag::findOrCreate('Practical', TagTypes::BLOG_CATEGORY->value);

    $tagged = BlogArticle::factory()->published()->create();
    $tagged->attachTag($tag);
    $untagged = BlogArticle::factory()->published()->create();

    $component = livewire(BlogArticleList::class);
    $component->call('filterByCategory', $tag->slug);

    $ids = collect($component->instance()->getLoadMoreData('articles'))->pluck('id');

    expect($ids)->toContain($tagged->id)
        ->and($ids)->not->toContain($untagged->id);
});
