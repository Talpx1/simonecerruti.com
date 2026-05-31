<?php

declare(strict_types=1);

use App\Enums\BlogArticleStatuses;
use App\Enums\RobotsDirective;
use App\Enums\SchemaType;
use App\Enums\TwitterCard;
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

describe('SEO overrides', function () {
    it('saves translatable SEO overrides to the active locale, preserving the other', function () {
        $article = BlogArticle::factory()->create();

        $component = livewire(EditBlogArticle::class, ['record' => $article->id]);

        $component->fillForm(['seo.title' => 'Titolo SEO'])
            ->call('save')
            ->assertHasNoFormErrors();

        $component->call('setActiveLocale', 'en')
            ->fillForm(['seo.title' => 'SEO Title'])
            ->call('save')
            ->assertHasNoFormErrors();

        $article->refresh()->load('seo');

        expect($article->seo->getTranslation('title', 'it', false))->toBe('Titolo SEO')
            ->and($article->seo->getTranslation('title', 'en', false))->toBe('SEO Title');
    });

    it('persists the non-translatable enum fields and robots directives', function () {
        $article = BlogArticle::factory()->create();

        livewire(EditBlogArticle::class, ['record' => $article->id])
            ->fillForm([
                'seo.schema_type' => SchemaType::ARTICLE->value,
                'seo.twitter_card' => TwitterCard::SUMMARY->value,
                'seo.robots' => [RobotsDirective::NOINDEX->value, RobotsDirective::NOFOLLOW->value],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $article->refresh()->load('seo');

        expect($article->seo->schema_type)->toBe(SchemaType::ARTICLE)
            ->and($article->seo->twitter_card)->toBe(TwitterCard::SUMMARY)
            ->and($article->seo->robots)->toBe([RobotsDirective::NOINDEX->value, RobotsDirective::NOFOLLOW->value]);
    });
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
