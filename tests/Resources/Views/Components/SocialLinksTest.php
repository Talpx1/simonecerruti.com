<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;

it('suffixes each social data-pan name with its placement', function (string $variant, string $placement): void {
    expect(Blade::render(
        '<x-social-links :variant="$variant" :placement="$placement" />',
        ['variant' => $variant, 'placement' => $placement],
    ))
        ->toContain("data-pan=\"cta-social-linkedin-{$placement}\"")
        ->toContain("data-pan=\"cta-social-github-{$placement}\"")
        ->not->toContain('data-pan="cta-social-linkedin"');
})->with([
    'menu placement' => ['menu', 'menu'],
    'footer placement' => ['foot', 'footer'],
    'icons placement' => ['icons', 'footer'],
    'contacts placement' => ['grid', 'contacts'],
]);

it('falls back to the variant when no placement is given', function (): void {
    expect(Blade::render('<x-social-links variant="icons" />'))
        ->toContain('data-pan="cta-social-linkedin-icons"');
});
