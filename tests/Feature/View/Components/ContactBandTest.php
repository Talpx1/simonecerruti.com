<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;

it('renders the closing band with the configured company email', function () {
    config()->set('company.contacts.email', 'band@example.test');

    $html = Blade::render('<x-contact-band />');

    expect($html)
        ->toContain('data-copy="band@example.test"')
        ->toContain('band@example.test')
        ->toContain(route('contacts'))
        ->toContain('data-pan="cta-band-contacts"');
});

it('renders the page hero rows as split headings with a canvas effect', function () {
    $html = Blade::render('<x-page-hero fx="pix" label="Hero label" :rows="[\'First row\', \'Second row\']">Intro</x-page-hero>');

    expect($html)
        ->toContain('aria-label="Hero label"')
        ->toContain('data-fx="pix"')
        ->toContain('First row')
        ->toContain('Second row')
        ->toContain('Intro');
});

it('omits the hero canvas when no effect is given', function () {
    $html = Blade::render('<x-page-hero label="Hero label" :rows="[\'Row\']" />');

    expect($html)->not->toContain('<canvas');
});
