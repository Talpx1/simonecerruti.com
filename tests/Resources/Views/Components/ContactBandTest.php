<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;

it('renders the closing band with the configured company email', function (): void {
    config()->set('company.contacts.email', 'band@example.test');

    expect(Blade::render('<x-contact-band />'))
        ->toContain('data-copy="band@example.test"')
        ->toContain('band@example.test')
        ->toContain(route('contacts'))
        ->toContain('data-pan="cta-band-contacts"');
});
