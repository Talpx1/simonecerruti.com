<?php

declare(strict_types=1);

use App\Livewire\Pages\BlogArticle\BlogArticleShow;
use App\Models\BlogArticle;

use function Pest\Livewire\livewire;

beforeEach(fn () => $this->withoutVite());

it('renders a blog article', function () {
    $article = BlogArticle::factory()->published()->create();

    $component = livewire(BlogArticleShow::class, ['blog_article' => $article])
        ->assertOk();

    expect($component->instance()->blog_article->id)->toBe($article->id);
});

it('exposes previous and next published articles', function () {
    $older = BlogArticle::factory()->published()->create(['published_at' => now()->subDays(3)]);
    $current = BlogArticle::factory()->published()->create(['published_at' => now()->subDays(2)]);
    $newer = BlogArticle::factory()->published()->create(['published_at' => now()->subDay()]);

    $component = livewire(BlogArticleShow::class, ['blog_article' => $current]);

    expect($component->instance()->previous->id)->toBe($older->id)
        ->and($component->instance()->next->id)->toBe($newer->id);
});
