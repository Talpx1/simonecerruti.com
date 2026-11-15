<?php

declare(strict_types=1);

use App\Livewire\Pages\TermsAndConditions;

it('renders the page with the switcher, the side index and the reading bar', function (): void {
    assertLegalPageRendersNavigation(TermsAndConditions::class, 'Terms');
});

it('keeps the page out of the index with a noindex robots tag', function (): void {
    getPageWithoutLocaleRedirects('terms_and_conditions')
        ->assertOk()
        ->assertSee('<meta name="robots" content="noindex">', escape: false);
});

it('anchors every side index entry to a heading of the legal text', function (): void {
    assertLegalPageAnchorsEverySideIndexEntry(TermsAndConditions::class);
});

it('reads the legal contact email from the company contacts config', function (): void {
    assertLegalPageReadsContactEmailFromConfig(TermsAndConditions::class);
});
