<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;

it('renders the rows as split headings with a canvas effect', function (): void {
    expect(Blade::render('<x-page-hero effect="pixel-paint" label="Hero label" :rows="[\'First row\', \'Second row\']">Intro</x-page-hero>'))
        ->toContain('aria-label="Hero label"')
        ->toContain('data-effect="pixel-paint"')
        ->toContain('First row')
        ->toContain('Second row')
        ->toContain('Intro');
});

it('omits the canvas when no effect is given', function (): void {
    expect(Blade::render('<x-page-hero label="Hero label" :rows="[\'Row\']" />'))
        ->not->toContain('<canvas');
});
