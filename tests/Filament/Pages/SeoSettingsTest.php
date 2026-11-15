<?php

declare(strict_types=1);

use App\Enums\SchemaType;
use App\Filament\Pages\SeoSettings;
use App\Models\SeoSetting;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    authenticateAsAdmin();
});

describe('access', function (): void {
    it('renders the settings page for an admin', function (): void {
        livewire(SeoSettings::class)->assertOk();
    });

    it('forbids users who cannot access the panel', function (): void {
        assertPanelUrlIsForbiddenForNonAdminUser(SeoSettings::getUrl());
    });

    it('redirects guests to the login page', function (): void {
        assertPanelUrlRedirectsGuestToLogin(SeoSettings::getUrl());
    });
});

describe('saving', function (): void {
    it('saves the SEO settings singleton with a per-locale description', function (): void {
        expect(SeoSetting::query()->where('name', 'Acme Inc')->exists())->toBeFalse();

        livewire(SeoSettings::class)
            ->fillForm([
                'type' => SchemaType::ORGANIZATION,
                'name' => 'Acme Inc',
                'description' => ['en' => 'We build things', 'it' => 'Costruiamo cose'],
                'social_profiles' => [['url' => 'https://twitter.com/acme']],
                'website_schema' => true,
                'default_og_image' => 'https://example.test/og.jpg',
                'title_separator' => ' - ',
            ])
            ->call('save')
            ->assertNotified()
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
});
