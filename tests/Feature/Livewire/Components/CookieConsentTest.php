<?php

declare(strict_types=1);

use App\Livewire\Components\CookieConsent;

use function Pest\Livewire\livewire;

beforeEach(fn () => $this->withoutVite());

it('shows the banner when no consent cookie is present', function () {
    livewire(CookieConsent::class)
        ->assertSet('show', true);
});

it('accepts all categories', function () {
    livewire(CookieConsent::class)
        ->call('acceptAll')
        ->assertSet('analytics', true)
        ->assertSet('functional', true)
        ->assertSet('marketing', true)
        ->assertSet('show', false)
        ->assertDispatched('consent-updated');
});

it('rejects all non-necessary categories', function () {
    livewire(CookieConsent::class)
        ->call('rejectAll')
        ->assertSet('analytics', false)
        ->assertSet('functional', false)
        ->assertSet('marketing', false)
        ->assertSet('show', false)
        ->assertDispatched('consent-updated');
});

it('reopens the banner via the open-cookie-banner event', function () {
    livewire(CookieConsent::class)
        ->call('openBanner')
        ->assertSet('show', true)
        ->assertSet('showPreferences', false);
});

it('withdraws consent', function () {
    livewire(CookieConsent::class)
        ->call('withdrawConsent')
        ->assertSet('show', true)
        ->assertSet('analytics', false)
        ->assertDispatched('consent-updated');
});

it('renders the summary banner with the artifact classes and the three actions', function () {
    livewire(CookieConsent::class)
        ->assertSeeHtml('class="cookie-banner is-visible"')
        ->assertSeeHtml('wire:click="acceptAll"')
        ->assertSeeHtml('wire:click="rejectAll"')
        ->assertSeeHtml('wire:click="openPreferences"')
        ->assertDontSeeHtml('class="cookie-banner-preferences"');
});

it('renders the four categories with switches in the preferences panel', function () {
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

it('saves the preferences chosen in the panel', function () {
    livewire(CookieConsent::class)
        ->call('openPreferences')
        ->set('analytics', true)
        ->call('savePreferences')
        ->assertSet('analytics', true)
        ->assertSet('functional', false)
        ->assertSet('show', false)
        ->assertDispatched('consent-updated');
});
