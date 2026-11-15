<?php

declare(strict_types=1);

use App\Actions\UpdateTagTranslations;
use App\Models\Tag;

it('updates the names and regenerates the slugs', function () {
    $tag = Tag::query()->create(['name' => ['it' => 'Gestionale', 'en' => 'Gestionale']]);

    UpdateTagTranslations::run($tag, ['it' => 'Gestionale', 'en' => 'Management software']);

    $tag->refresh();

    expect($tag->getTranslationWithoutFallback('name', 'en'))->toBe('Management software')
        ->and($tag->getTranslationWithoutFallback('slug', 'en'))->toBe('management-software')
        ->and($tag->getTranslationWithoutFallback('slug', 'it'))->toBe('gestionale');
});

it('ignores blank names and unsupported locales', function () {
    $tag = Tag::query()->create(['name' => ['it' => 'Gestionale', 'en' => 'Management software']]);

    UpdateTagTranslations::run($tag, ['it' => '', 'en' => null, 'xx' => 'Nope']);

    expect($tag->refresh()->getTranslations('name'))->toBe(['it' => 'Gestionale', 'en' => 'Management software']);
});
