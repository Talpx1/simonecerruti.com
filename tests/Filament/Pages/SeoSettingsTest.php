<?php

declare(strict_types=1);

use App\Enums\SchemaType;
use App\Filament\Pages\SeoSettings;
use App\Models\SeoSetting;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->withoutVite();
    actingAsAdmin();
});

it('renders the settings page', function () {
    livewire(SeoSettings::class)->assertOk();
});

it('saves the SEO settings singleton with a per-locale description', function () {
    livewire(SeoSettings::class)
        ->fillForm([
            'type' => SchemaType::ORGANIZATION->value,
            'name' => 'Acme Inc',
            'description' => ['en' => 'We build things', 'it' => 'Costruiamo cose'],
            'social_profiles' => [['url' => 'https://twitter.com/acme']],
            'website_schema' => true,
            'default_og_image' => 'https://example.test/og.jpg',
            'title_separator' => ' - ',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $setting = SeoSetting::query()->firstOrFail();

    expect($setting->type)->toBe(SchemaType::ORGANIZATION)
        ->and($setting->name)->toBe('Acme Inc')
        ->and($setting->getTranslation('description', 'en', false))->toBe('We build things')
        ->and($setting->getTranslation('description', 'it', false))->toBe('Costruiamo cose')
        ->and($setting->social_profiles)->toBe(['https://twitter.com/acme'])
        ->and($setting->website_schema)->toBeTrue()
        ->and($setting->title_separator)->toBe(' - ');
});
