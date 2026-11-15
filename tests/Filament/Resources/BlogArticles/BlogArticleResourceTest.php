<?php

declare(strict_types=1);

use App\Enums\BlogArticleStatuses;
use App\Enums\RobotsDirective;
use App\Enums\SchemaType;
use App\Enums\TwitterCard;
use App\Filament\Resources\BlogArticles\BlogArticleResource;
use App\Filament\Resources\BlogArticles\Pages\CreateBlogArticle;
use App\Filament\Resources\BlogArticles\Pages\EditBlogArticle;
use App\Filament\Resources\BlogArticles\Pages\ListBlogArticles;
use App\Models\BlogArticle;
use App\Models\Seo;
use Illuminate\Support\Facades\Auth;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;

beforeEach(function (): void {
    authenticateAsAdmin();
});

describe('access', function (): void {
    it('forbids users who cannot access the panel', function (): void {
        $article = BlogArticle::factory()->create();

        assertPanelUrlIsForbiddenForNonAdminUser(BlogArticleResource::getUrl('index'));
        assertPanelUrlIsForbiddenForNonAdminUser(BlogArticleResource::getUrl('create'));
        assertPanelUrlIsForbiddenForNonAdminUser(BlogArticleResource::getUrl('edit', ['record' => $article]));
    });

    it('redirects guests to the login page', function (): void {
        $article = BlogArticle::factory()->create();

        assertPanelUrlRedirectsGuestToLogin(BlogArticleResource::getUrl('index'));
        assertPanelUrlRedirectsGuestToLogin(BlogArticleResource::getUrl('create'));
        assertPanelUrlRedirectsGuestToLogin(BlogArticleResource::getUrl('edit', ['record' => $article]));
    });
});

describe('list page', function (): void {
    it('lists blog articles', function (): void {
        $articles = BlogArticle::factory()->count(3)->create();

        livewire(ListBlogArticles::class)
            ->assertCanSeeTableRecords($articles);
    });

    it('searches articles by title', function (): void {
        $found = BlogArticle::factory()->withTitle('Findable Title')->create();
        $other = BlogArticle::factory()->withTitle('Hidden Title')->create();

        livewire(ListBlogArticles::class)
            ->searchTable('Findable')
            ->assertCanSeeTableRecords([$found])
            ->assertCanNotSeeTableRecords([$other]);
    });
});

describe('create page', function (): void {
    it('creates an article and assigns the authenticated user as author', function (): void {
        expect(BlogArticle::query()->count())->toBe(0);

        livewire(CreateBlogArticle::class)
            ->fillForm([
                'title' => 'My First Article',
                'slug' => 'my-first-article',
                'summary' => 'A short summary.',
                'content' => '<p>The article content.</p>',
                'status' => BlogArticleStatuses::PUBLISHED->value,
            ])
            ->call('create')
            ->assertNotified()
            ->assertHasNoFormErrors();

        $article = BlogArticle::query()->latest('id')->first();

        expect(BlogArticle::query()->count())->toBe(1)
            ->and($article)->not->toBeNull()
            ->and($article->author_id)->toBe(Auth::id())
            ->and($article->status)->toBe(BlogArticleStatuses::PUBLISHED)
            ->and($article->published_at)->not->toBeNull();
    });

    it('validates the required fields', function (): void {
        livewire(CreateBlogArticle::class)
            ->fillForm([
                'title' => null,
                'slug' => null,
                'summary' => null,
                'content' => null,
                'status' => null,
            ])
            ->call('create')
            ->assertHasFormErrors([
                'title' => 'required',
                'slug' => 'required',
                'summary' => 'required',
                'status' => 'required',
            ])
            ->assertHasFormErrors(['content'])
            ->assertNotNotified();

        expect(BlogArticle::query()->count())->toBe(0);
    });
});

describe('edit page', function (): void {
    it('saves an article whose content registers the gallery block', function (): void {
        $article = BlogArticle::factory()->create();

        expect($article->getTranslation('content', 'it'))->not->toContain('Updated with gallery support.');

        livewire(EditBlogArticle::class, ['record' => $article->id])
            ->fillForm(['content' => '<p>Updated with gallery support.</p>'])
            ->call('save')
            ->assertNotified()
            ->assertHasNoFormErrors();

        expect($article->refresh()->getTranslation('content', 'it'))->toContain('Updated with gallery support.');
    });

    describe('SEO overrides', function (): void {
        it('saves translatable SEO overrides to the active locale, preserving the other', function (): void {
            $article = BlogArticle::factory()->create();

            expect($article->seo)->toBeNull();

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

        it('creates no Seo row when the section is left blank', function (): void {
            $article = BlogArticle::factory()->create();

            livewire(EditBlogArticle::class, ['record' => $article->id])
                ->call('save')
                ->assertHasNoFormErrors();

            expect(Seo::query()->where('seoable_id', $article->id)->exists())->toBeFalse();
        });

        it('prunes the Seo row once every override is cleared', function (): void {
            $article = BlogArticle::factory()->create();
            Seo::factory()->for($article, 'seoable')->create([
                'title' => ['it' => 'Temporaneo'],
                'description' => null,
                'schema_type' => null,
            ]);

            expect(Seo::query()->where('seoable_id', $article->id)->exists())->toBeTrue();

            livewire(EditBlogArticle::class, ['record' => $article->id])
                ->fillForm(['seo.title' => null])
                ->call('save')
                ->assertHasNoFormErrors();

            expect(Seo::query()->where('seoable_id', $article->id)->exists())->toBeFalse();
        });

        it('persists the non-translatable enum fields and robots directives', function (): void {
            $article = BlogArticle::factory()->create();

            expect($article->seo)->toBeNull();

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
                ->and($article->seo->getTranslation('robots', 'it', false))->toBe([RobotsDirective::NOINDEX->value, RobotsDirective::NOFOLLOW->value]);
        });
    });

    describe('published_at stamping', function (): void {
        it('does not stamp published_at when saving a draft', function (): void {
            $article = BlogArticle::factory()->draft()->create();

            expect($article->published_at)->toBeNull();

            livewire(EditBlogArticle::class, ['record' => $article->id])
                ->fillForm(['status' => BlogArticleStatuses::DRAFT->value])
                ->call('save')
                ->assertHasNoFormErrors();

            assertDatabaseHas(BlogArticle::class, [
                'id' => $article->id,
                'status' => BlogArticleStatuses::DRAFT->value,
                'published_at' => null,
            ]);
        });

        it('stamps published_at when publishing through the edit page', function (): void {
            $article = BlogArticle::factory()->draft()->create();

            expect($article->published_at)->toBeNull();

            livewire(EditBlogArticle::class, ['record' => $article->id])
                ->fillForm(['status' => BlogArticleStatuses::PUBLISHED->value])
                ->call('save')
                ->assertHasNoFormErrors();

            expect($article->refresh()->published_at)->not->toBeNull();
        });
    });
});
