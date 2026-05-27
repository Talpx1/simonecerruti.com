<?php

declare(strict_types=1);

use App\Enums\BlogArticleStatuses;
use App\Filament\Resources\BlogArticles\Pages\CreateBlogArticle;
use App\Filament\Resources\BlogArticles\Pages\EditBlogArticle;
use App\Filament\Resources\BlogArticles\Pages\ListBlogArticles;
use App\Models\BlogArticle;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->withoutVite();
    $this->admin = actingAsAdmin();
});

it('lists blog articles', function () {
    $articles = BlogArticle::factory()->count(3)->create();

    livewire(ListBlogArticles::class)
        ->assertCanSeeTableRecords($articles);
});

it('searches articles by title', function () {
    $found = BlogArticle::factory()->create(['title' => ['en' => 'Findable Title', 'it' => 'Findable Title']]);
    $other = BlogArticle::factory()->create(['title' => ['en' => 'Hidden Title', 'it' => 'Hidden Title']]);

    livewire(ListBlogArticles::class)
        ->searchTable('Findable')
        ->assertCanSeeTableRecords([$found])
        ->assertCanNotSeeTableRecords([$other]);
});

it('creates an article and assigns the authenticated user as author', function () {
    livewire(CreateBlogArticle::class)
        ->fillForm([
            'title' => 'My First Article',
            'slug' => 'my-first-article',
            'summary' => 'A short summary.',
            'content' => '<p>The article content.</p>',
            'status' => BlogArticleStatuses::PUBLISHED->value,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $article = BlogArticle::query()->latest('id')->first();

    expect($article)->not->toBeNull()
        ->and($article->author_id)->toBe($this->admin->id)
        ->and($article->published_at)->not->toBeNull();
});

it('validates the required fields on create', function () {
    livewire(CreateBlogArticle::class)
        ->fillForm([
            'title' => null,
            'slug' => null,
            'summary' => null,
            'content' => null,
            'status' => null,
        ])
        ->call('create')
        ->assertHasFormErrors(['title', 'slug', 'summary', 'content', 'status']);
});

describe('published_at stamping (regression for the ?? precedence bug)', function () {
    it('does not stamp published_at when saving a draft', function () {
        $article = BlogArticle::factory()->draft()->create(['published_at' => null]);

        livewire(EditBlogArticle::class, ['record' => $article->id])
            ->fillForm(['status' => BlogArticleStatuses::DRAFT->value])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($article->refresh()->published_at)->toBeNull();
    });

    it('stamps published_at when publishing through the edit page', function () {
        $article = BlogArticle::factory()->draft()->create(['published_at' => null]);

        livewire(EditBlogArticle::class, ['record' => $article->id])
            ->fillForm(['status' => BlogArticleStatuses::PUBLISHED->value])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($article->refresh()->published_at)->not->toBeNull();
    });
});
