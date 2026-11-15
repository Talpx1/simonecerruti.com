<?php

declare(strict_types=1);

use App\Livewire\Components\CookieConsent;
use Illuminate\Support\Facades\Cookie;

use function Pest\Livewire\livewire;

describe('banner', function (): void {
    it('shows when no consent cookie is present', function (): void {
        livewire(CookieConsent::class)
            ->assertSet('show', true);
    });

    it('reopens via the open-cookie-banner event', function (): void {
        livewire(CookieConsent::class)
            ->call('acceptAll')
            ->assertSet('show', false)
            ->call('openBanner')
            ->assertSet('show', true)
            ->assertSet('showPreferences', false);
    });

    it('renders the summary with the artifact classes and the three actions', function (): void {
        livewire(CookieConsent::class)
            ->assertSeeHtml('class="cookie-banner is-visible"')
            ->assertSeeHtml('wire:click="acceptAll"')
            ->assertSeeHtml('wire:click="rejectAll"')
            ->assertSeeHtml('wire:click="openPreferences"')
            ->assertDontSeeHtml('class="cookie-banner-preferences"');
    });

    it('renders the four categories with switches in the preferences panel', function (): void {
        livewire(CookieConsent::class)
            ->call('openPreferences')
            ->assertSeeHtml('class="cookie-banner is-visible is-open"')
            ->assertSeeHtml('class="cookie-banner-preferences"')
            ->assertSeeHtml('id="toggle-analytics"')
            ->assertSeeHtml('id="toggle-functional"')
            ->assertSeeHtml('id="toggle-marketing"')
            ->assertSee(__('Strictly necessary'))
            ->assertSee(__('coming soon'))
            ->assertSeeHtml('wire:click="savePreferences"');
    });
});

describe('consent choices', function (): void {
    it('accepts all categories', function (): void {
        livewire(CookieConsent::class)
            ->assertSet('analytics', false)
            ->assertSet('functional', false)
            ->assertSet('marketing', false)
            ->call('acceptAll')
            ->assertSet('analytics', true)
            ->assertSet('functional', true)
            ->assertSet('marketing', true)
            ->assertSet('show', false)
            ->assertDispatched('consent-updated');
    });

    it('rejects all non-necessary categories', function (): void {
        livewire(CookieConsent::class)
            ->call('acceptAll')
            ->assertSet('analytics', true)
            ->call('rejectAll')
            ->assertSet('analytics', false)
            ->assertSet('functional', false)
            ->assertSet('marketing', false)
            ->assertSet('show', false)
            ->assertDispatched('consent-updated');
    });

    it('saves the preferences chosen in the panel', function (): void {
        livewire(CookieConsent::class)
            ->call('openPreferences')
            ->set('analytics', true)
            ->call('savePreferences')
            ->assertSet('analytics', true)
            ->assertSet('functional', false)
            ->assertSet('show', false)
            ->assertDispatched('consent-updated');
    });

    it('queues the consent cookie when the choice is saved', function (): void {
        expect(Cookie::hasQueued(CookieConsent::COOKIE_NAME))->toBeFalse();

        livewire(CookieConsent::class)->call('acceptAll');

        expect(Cookie::hasQueued(CookieConsent::COOKIE_NAME))->toBeTrue();
    });
});

describe('withdrawal', function (): void {
    it('withdraws consent and removes the consent cookie', function (): void {
        $component = livewire(CookieConsent::class)
            ->call('acceptAll')
            ->assertSet('analytics', true)
            ->assertSet('show', false);

        expect(Cookie::queued(CookieConsent::COOKIE_NAME)?->isCleared())->toBeFalse();

        $component->call('withdrawConsent')
            ->assertSet('show', true)
            ->assertSet('analytics', false)
            ->assertDispatched('consent-updated');

        expect(Cookie::queued(CookieConsent::COOKIE_NAME)?->isCleared())->toBeTrue();
    });
});
