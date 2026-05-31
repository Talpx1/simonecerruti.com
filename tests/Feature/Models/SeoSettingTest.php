<?php

declare(strict_types=1);

use App\Enums\SchemaType;
use App\Models\SeoSetting;

it('provides a default unsaved singleton when none exists', function () {
    $settings = SeoSetting::current();

    expect($settings->exists)->toBeFalse()
        ->and($settings->type)->toBe(SchemaType::PERSON)
        ->and($settings->title_separator)->toBe(' | ')
        ->and($settings->website_schema)->toBeTrue();
});

it('returns the persisted singleton once saved', function () {
    SeoSetting::factory()->create(['title_separator' => ' — ']);

    expect(SeoSetting::current()->exists)->toBeTrue()
        ->and(SeoSetting::current()->title_separator)->toBe(' — ');
});

it('invalidates the cached singleton when settings are saved', function () {
    // Prime the cache with the default (no row yet).
    expect(SeoSetting::current()->title_separator)->toBe(' | ');

    SeoSetting::factory()->create(['title_separator' => ' · ']);

    expect(SeoSetting::current()->title_separator)->toBe(' · ');
});

it('stores the description translatably and casts json columns', function () {
    $settings = SeoSetting::factory()->create([
        'description' => ['en' => 'Bio', 'it' => 'Biografia'],
        'social_profiles' => ['https://example.test/a', 'https://example.test/b'],
        'website_schema' => false,
    ]);

    $settings->refresh();

    expect($settings->getTranslation('description', 'it'))->toBe('Biografia')
        ->and($settings->social_profiles)->toBe(['https://example.test/a', 'https://example.test/b'])
        ->and($settings->website_schema)->toBeFalse();
});
