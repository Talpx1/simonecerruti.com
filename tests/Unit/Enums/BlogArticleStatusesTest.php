<?php

declare(strict_types=1);

use App\Enums\BlogArticleStatuses;
use Filament\Support\Colors\Color;
use Illuminate\Support\Collection;

describe('allowsCrawling', function () {
    it('knows which statuses allow crawling', function (BlogArticleStatuses $status, bool $expected) {
        expect($status->allowsCrawling())->toBe($expected);
    })->with([
        'draft' => [BlogArticleStatuses::DRAFT, false],
        'published' => [BlogArticleStatuses::PUBLISHED, true],
        'archived' => [BlogArticleStatuses::ARCHIVED, true],
        'hidden' => [BlogArticleStatuses::HIDDEN, false],
    ]);
});

describe('getColor', function () {
    it('maps each status to a color', function () {
        expect(BlogArticleStatuses::DRAFT->getColor())->toBe(Color::Yellow)
            ->and(BlogArticleStatuses::PUBLISHED->getColor())->toBe(Color::Green)
            ->and(BlogArticleStatuses::ARCHIVED->getColor())->toBe(Color::Slate)
            ->and(BlogArticleStatuses::HIDDEN->getColor())->toBe(Color::Gray);
    });
});

describe('collect', function () {
    it('returns a collection of every case', function () {
        expect(BlogArticleStatuses::collect())->toBeInstanceOf(Collection::class)
            ->toHaveCount(4)
            ->and(BlogArticleStatuses::collect()->all())->toBe(BlogArticleStatuses::cases());
    });
});

describe('random', function () {
    it('returns a valid case', function () {
        expect(BlogArticleStatuses::cases())->toContain(BlogArticleStatuses::random());
    });
});

describe('localized label and description', function () {
    it('builds the label from a localized translation key', function () {
        app('translator')->addLines(['enums.blog_article_statuses.draft.label' => 'Bozza'], 'it');

        expect(BlogArticleStatuses::DRAFT->getLabel('it'))->toBe('Bozza');
    });

    it('builds the description from a localized translation key', function () {
        app('translator')->addLines(
            ['enums.blog_article_statuses.draft.description' => 'A draft article'],
            app()->getLocale(),
        );

        expect(BlogArticleStatuses::DRAFT->getDescription())->toBe('A draft article');
    });
});
