<?php

declare(strict_types=1);

use App\Enums\BlogArticleStatuses;
use Filament\Support\Colors\Color;

describe('allowsCrawling', function (): void {
    it('tells whether the status allows crawling', function (BlogArticleStatuses $status): void {
        $expected = [
            BlogArticleStatuses::DRAFT->value => false,
            BlogArticleStatuses::PUBLISHED->value => true,
            BlogArticleStatuses::ARCHIVED->value => true,
            BlogArticleStatuses::HIDDEN->value => false,
        ];

        expect($status->allowsCrawling())->toBe($expected[$status->value]);
    })->with(BlogArticleStatuses::cases());
});

describe('getColor', function (): void {
    it('maps the status to its color', function (BlogArticleStatuses $status): void {
        $expected = [
            BlogArticleStatuses::DRAFT->value => Color::Yellow,
            BlogArticleStatuses::PUBLISHED->value => Color::Green,
            BlogArticleStatuses::ARCHIVED->value => Color::Slate,
            BlogArticleStatuses::HIDDEN->value => Color::Gray,
        ];

        expect($status->getColor())->toBe($expected[$status->value]);
    })->with(BlogArticleStatuses::cases());
});

describe('getLabel', function (): void {
    it('translates the label in the requested locale', function (BlogArticleStatuses $status, string $locale): void {
        $expected = [
            'en' => [
                BlogArticleStatuses::DRAFT->value => 'Draft',
                BlogArticleStatuses::PUBLISHED->value => 'Published',
                BlogArticleStatuses::ARCHIVED->value => 'Archived',
                BlogArticleStatuses::HIDDEN->value => 'Hidden',
            ],
            'it' => [
                BlogArticleStatuses::DRAFT->value => 'Bozza',
                BlogArticleStatuses::PUBLISHED->value => 'Pubblicato',
                BlogArticleStatuses::ARCHIVED->value => 'Archiviato',
                BlogArticleStatuses::HIDDEN->value => 'Nascosto',
            ],
        ];

        expect($status->getLabel($locale))->toBe($expected[$locale][$status->value]);
    })->with(BlogArticleStatuses::cases())->with(['en', 'it']);
});

describe('getDescription', function (): void {
    it('translates the description in the current locale', function (BlogArticleStatuses $status, string $locale): void {
        $description = runWithLocale($locale, fn (): string => $status->getDescription());

        expect($description)->not->toBeEmpty()->not->toStartWith('enums.');
    })->with(BlogArticleStatuses::cases())->with(['en', 'it']);

    it('differs between locales', function (BlogArticleStatuses $status): void {
        $english = runWithLocale('en', fn (): string => $status->getDescription());
        $italian = runWithLocale('it', fn (): string => $status->getDescription());

        expect($english)->not->toBe($italian);
    })->with(BlogArticleStatuses::cases());
});
