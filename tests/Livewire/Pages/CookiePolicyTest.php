<?php

declare(strict_types=1);

use App\Livewire\Pages\CookiePolicy;

it('renders the page with the switcher, the side index and the reading bar', function (): void {
    assertLegalPageRendersNavigation(CookiePolicy::class, 'Cookies');
});

it('keeps the page out of the index with a noindex robots tag', function (): void {
    getPageWithoutLocaleRedirects('cookie_policy')
        ->assertOk()
        ->assertSee('<meta name="robots" content="noindex">', escape: false);
});

it('anchors every side index entry to a heading of the legal text', function (): void {
    assertLegalPageAnchorsEverySideIndexEntry(CookiePolicy::class);
});

it('reads the legal contact email from the company contacts config', function (): void {
    assertLegalPageReadsContactEmailFromConfig(CookiePolicy::class);
});
