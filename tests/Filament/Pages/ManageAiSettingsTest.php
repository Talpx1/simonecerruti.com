<?php

declare(strict_types=1);

use App\Enums\GeneratableFields;
use App\Filament\Pages\ManageAiSettings;
use App\Settings\AiSettings;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    authenticateAsAdmin();
});

describe('access', function (): void {
    it('renders the AI settings page for an admin', function (): void {
        livewire(ManageAiSettings::class)->assertOk();
    });

    it('forbids users who cannot access the panel', function (): void {
        assertPanelUrlIsForbiddenForNonAdminUser(ManageAiSettings::getUrl());
    });

    it('redirects guests to the login page', function (): void {
        assertPanelUrlRedirectsGuestToLogin(ManageAiSettings::getUrl());
    });
});

describe('saving', function (): void {
    it('saves the style guide and the prompt overrides', function (): void {
        livewire(ManageAiSettings::class)
            ->fillForm([
                'style_guide' => 'Be brief.',
                'prompts' => [GeneratableFields::SEO_TITLE->value => 'Custom SEO title prompt'],
            ])
            ->call('save')
            ->assertNotified()
            ->assertHasNoFormErrors();

        $settings = resolve(AiSettings::class);

        expect($settings->style_guide)->toBe('Be brief.')
            ->and($settings->prompts)->toBe([GeneratableFields::SEO_TITLE->value => 'Custom SEO title prompt']);
    });

    it('drops blank prompt overrides so the default applies again', function (): void {
        livewire(ManageAiSettings::class)
            ->fillForm([
                'style_guide' => 'Be brief.',
                'prompts' => [GeneratableFields::SEO_TITLE->value => ''],
            ])
            ->call('save');

        expect(resolve(AiSettings::class)->prompts)->toBeEmpty();
    });

    it('requires a style guide', function (): void {
        livewire(ManageAiSettings::class)
            ->fillForm(['style_guide' => ''])
            ->call('save')
            ->assertHasFormErrors(['style_guide' => 'required']);
    });
});
