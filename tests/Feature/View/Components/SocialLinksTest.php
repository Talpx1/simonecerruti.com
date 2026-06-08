<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;

it('suffixes each social data-pan name with its placement', function (string $variant, string $placement) {
    $html = Blade::render(
        '<x-social-links :variant="$variant" :placement="$placement" />',
        ['variant' => $variant, 'placement' => $placement],
    );

    expect($html)
        ->toContain("data-pan=\"cta-social-linkedin-{$placement}\"")
        ->toContain("data-pan=\"cta-social-github-{$placement}\"");

    // The bare, un-suffixed name must never leak alongside the suffixed one.
    expect(\str_contains($html, 'data-pan="cta-social-linkedin"'))->toBeFalse();
})->with([
    'menu placement' => ['menu', 'menu'],
    'footer placement' => ['icons', 'footer'],
    'contacts placement' => ['detailed', 'contacts'],
]);

it('falls back to the variant when no placement is given', function () {
    $html = Blade::render('<x-social-links variant="icons" />');

    expect($html)->toContain('data-pan="cta-social-linkedin-icons"');
});
