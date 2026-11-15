<?php

declare(strict_types=1);

use App\Enums\GeneratableFields;

describe('catalogue', function (): void {
    it('has a default prompt, a subject, a group label and context fields', function (GeneratableFields $field): void {
        expect($field->getDefaultPrompt())->not->toBeEmpty()
            ->and($field->getSubject())->not->toBeEmpty()
            ->and($field->getGroupLabel())->not->toBeEmpty()
            ->and($field->getContextFields())->not->toBeEmpty();
    })->with(GeneratableFields::cases());

    it('has a label in every locale', function (GeneratableFields $field, string $locale): void {
        expect($field->getLabel($locale))->not->toStartWith('enums.');
    })->with(GeneratableFields::cases())->with(['it', 'en']);
});

describe('isRichText', function (): void {
    it('flags only the rich editor fields', function (): void {
        $rich = collect(GeneratableFields::cases())->filter->isRichText()->values()->all();

        expect($rich)->toBe([
            GeneratableFields::BLOG_ARTICLE_CONTENT,
            GeneratableFields::PROJECT_DESCRIPTION,
            GeneratableFields::PROJECT_HOME_SUMMARY,
        ]);
    });
});

describe('getMaxCharacters', function (): void {
    it('mirrors the form limits', function (): void {
        expect(GeneratableFields::PROJECT_SHORT_DESCRIPTION->getMaxCharacters())->toBe(500)
            ->and(GeneratableFields::BLOG_ARTICLE_TITLE->getMaxCharacters())->toBe(255)
            ->and(GeneratableFields::BLOG_ARTICLE_CONTENT->getMaxCharacters())->toBeNull();
    });
});
