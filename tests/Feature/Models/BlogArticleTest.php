<?php

declare(strict_types=1);

use App\Enums\BlogArticleStatuses;
use App\Models\BlogArticle;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

describe('wherePublished scope', function () {
    it('only returns published articles with a past publish date', function () {
        $published = BlogArticle::factory()->published()->create();
        $draft = BlogArticle::factory()->draft()->create();
        $scheduled = BlogArticle::factory()->scheduled()->create();
        $archived = BlogArticle::factory()->archived()->create();
        $hidden = BlogArticle::factory()->hidden()->create();
        $unpublished = BlogArticle::factory()->unpublished()->create();

        $result = BlogArticle::query()->wherePublished()->get();

        expect($result->contains($published))->toBeTrue()
            ->and($result->contains($draft))->toBeFalse()
            ->and($result->contains($scheduled))->toBeFalse()
            ->and($result->contains($archived))->toBeFalse()
            ->and($result->contains($hidden))->toBeFalse()
            ->and($result->contains($unpublished))->toBeFalse();
    });
});

describe('whereCanBeCrawled scope', function () {
    it('returns crawlable statuses with a past publish date', function () {
        $published = BlogArticle::factory()->published()->create();
        $archived = BlogArticle::factory()->archived()->create();
        $draft = BlogArticle::factory()->draft()->create();
        $hidden = BlogArticle::factory()->hidden()->create();
        $scheduled = BlogArticle::factory()->scheduled()->create();

        $result = BlogArticle::query()->whereCanBeCrawled()->get();

        expect($result->contains($published))->toBeTrue()
            ->and($result->contains($archived))->toBeTrue()
            ->and($result->contains($draft))->toBeFalse()
            ->and($result->contains($hidden))->toBeFalse()
            ->and($result->contains($scheduled))->toBeFalse();
    });
});

describe('can_be_crawled attribute', function () {
    it('is true only for crawlable statuses published in the past', function (BlogArticleStatuses $status, bool $expected) {
        $article = BlogArticle::factory()->create([
            'status' => $status,
            'published_at' => now()->subDay(),
        ]);

        expect($article->can_be_crawled)->toBe($expected);
    })->with([
        'draft' => [BlogArticleStatuses::DRAFT, false],
        'published' => [BlogArticleStatuses::PUBLISHED, true],
        'archived' => [BlogArticleStatuses::ARCHIVED, true],
        'hidden' => [BlogArticleStatuses::HIDDEN, false],
    ]);

    it('is false when the publish date is in the future', function () {
        $article = BlogArticle::factory()->scheduled()->create();

        expect($article->can_be_crawled)->toBeFalse();
    });

    it('is false when there is no publish date', function () {
        $article = BlogArticle::factory()->create([
            'status' => BlogArticleStatuses::PUBLISHED,
            'published_at' => null,
        ]);

        expect($article->can_be_crawled)->toBeFalse();
    });
});

describe('summary_or_excerpt attribute', function () {
    it('returns the summary when present', function () {
        $article = BlogArticle::factory()->create();

        withLocale('en', function () use ($article) {
            expect($article->summary_or_excerpt)->toBe($article->summary);
        });
    });
});

describe('featured_image_url attribute', function () {
    it('falls back to the bundled placeholder when no media is attached', function () {
        $article = BlogArticle::factory()->create();

        expect($article->featured_image_url)->toBe(asset('images/fallback.jpg'));
    });

    it('returns a media url when a featured image is attached', function () {
        Storage::fake('public');

        $article = BlogArticle::factory()->create();
        $article->addMedia(UploadedFile::fake()->image('featured.jpg', 1920, 1080))
            ->toMediaCollection('featured_image');

        expect($article->refresh()->featured_image_url)->not->toBe(asset('images/fallback.jpg'));
    });
});

describe('relationships', function () {
    it('belongs to an author', function () {
        $user = User::factory()->create();
        $article = BlogArticle::factory()->for($user, 'author')->create();

        expect($article->author)->toBeInstanceOf(User::class)
            ->and($article->author->id)->toBe($user->id);
    });

    it('relates to projects through the relatables morph', function () {
        $project = Project::factory()->create();
        $article = BlogArticle::factory()->create();
        $article->relatables()->create([
            'relatable_type' => Project::class,
            'relatable_id' => $project->id,
        ]);

        expect($article->relatables)->toHaveCount(1)
            ->and($article->projects->pluck('id')->all())->toContain($project->id);
    });
});

describe('previous and next', function () {
    it('navigates published articles by publish date', function () {
        $older = BlogArticle::factory()->published()->create(['published_at' => now()->subDays(3)]);
        $mid = BlogArticle::factory()->published()->create(['published_at' => now()->subDays(2)]);
        $newer = BlogArticle::factory()->published()->create(['published_at' => now()->subDay()]);

        expect($mid->previous()->id)->toBe($older->id)
            ->and($mid->next()->id)->toBe($newer->id)
            ->and($newer->next())->toBeNull()
            ->and($older->previous())->toBeNull();
    });

    it('returns null for an article without a publish date', function () {
        $draft = BlogArticle::factory()->draft()->create();

        expect($draft->previous())->toBeNull()
            ->and($draft->next())->toBeNull();
    });
});

describe('relatedBlogArticles', function () {
    it('orders related articles by the number of shared tags', function () {
        $article = BlogArticle::factory()->published()->create();
        $article->attachTags(['php', 'laravel']);

        $sharesTwo = BlogArticle::factory()->published()->create();
        $sharesTwo->attachTags(['php', 'laravel']);

        $sharesOne = BlogArticle::factory()->published()->create();
        $sharesOne->attachTags(['php']);

        $sharesNone = BlogArticle::factory()->published()->create();
        $sharesNone->attachTags(['design']);

        $related = $article->relatedBlogArticles();

        expect($related->pluck('id')->all())->not->toContain($article->id)
            ->and($related->first()->id)->toBe($sharesTwo->id);
    });

    it('falls back to recent published articles when the source has no tags', function () {
        $article = BlogArticle::factory()->published()->create();
        $other = BlogArticle::factory()->published()->create(['published_at' => now()->subDay()]);

        $related = $article->relatedBlogArticles();

        expect($related->pluck('id')->all())->toContain($other->id)
            ->and($related->pluck('id')->all())->not->toContain($article->id);
    });
});
