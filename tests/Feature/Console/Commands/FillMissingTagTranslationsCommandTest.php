<?php

declare(strict_types=1);

use App\Models\Tag;

it('fills the missing translations of every tag', function () {
    $incomplete = Tag::query()->create(['name' => ['it' => 'Incompleto']]);
    $complete = Tag::query()->create(['name' => ['it' => 'Completo', 'en' => 'Complete']]);

    $this->artisan('tags:fill-missing-translations')
        ->expectsOutputToContain('Updated 1 tags.')
        ->assertSuccessful();

    expect($incomplete->refresh()->getTranslationWithoutFallback('name', 'en'))->toBe('Incompleto')
        ->and($complete->refresh()->getTranslationWithoutFallback('name', 'en'))->toBe('Complete');
});
