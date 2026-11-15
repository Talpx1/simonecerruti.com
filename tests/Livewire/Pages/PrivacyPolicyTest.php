<?php

declare(strict_types=1);

use App\Livewire\Pages\PrivacyPolicy;

it('renders the page with the switcher, the side index and the reading bar', function (): void {
    assertLegalPageRendersNavigation(PrivacyPolicy::class, 'Privacy');
});

it('keeps the page out of the index with a noindex robots tag', function (): void {
    getPageWithoutLocaleRedirects('privacy_policy')
        ->assertOk()
        ->assertSee('<meta name="robots" content="noindex">', escape: false);
});

it('anchors every side index entry to a heading of the legal text', function (): void {
    assertLegalPageAnchorsEverySideIndexEntry(PrivacyPolicy::class);
});

it('reads the legal contact email from the company contacts config', function (): void {
    assertLegalPageReadsContactEmailFromConfig(PrivacyPolicy::class);
});
