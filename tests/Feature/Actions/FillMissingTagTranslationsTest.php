<?php

declare(strict_types=1);

use App\Actions\FillMissingTagTranslations;
use App\Models\Tag;

it('copies the name and the slug into the locales without a translation', function () {
    $tag = Tag::query()->create(['name' => ['it' => 'Gestionale']]);

    FillMissingTagTranslations::run($tag);

    $tag->refresh();

    expect($tag->getTranslationWithoutFallback('name', 'en'))->toBe('Gestionale')
        ->and($tag->getTranslationWithoutFallback('slug', 'en'))->toBe('gestionale')
        ->and($tag->getTranslationWithoutFallback('slug', 'it'))->toBe('gestionale');
});

it('keeps the translations that already exist', function () {
    $tag = Tag::query()->create(['name' => ['it' => 'Gestionale', 'en' => 'Management software']]);

    FillMissingTagTranslations::run($tag);

    expect($tag->refresh()->getTranslationWithoutFallback('name', 'en'))->toBe('Management software');
});

it('uses the only translated locale as source when the app locale has none', function () {
    $tag = Tag::query()->create(['name' => ['en' => 'Plugin']]);

    withLocale('it', fn () => FillMissingTagTranslations::run($tag));

    expect($tag->refresh()->getTranslationWithoutFallback('name', 'it'))->toBe('Plugin');
});
