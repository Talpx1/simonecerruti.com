<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;

it('renders the photo card with responsive sources, the logo wave and the cursor glow', function (): void {
    expect(Blade::render('<x-portrait-showcase alternative-text="A friendly portrait" />'))
        ->toContain('data-effect="portrait-showcase"')
        ->toContain('alt="A friendly portrait"')
        ->toContain('images/simone-cerruti.jpg')
        ->toContain('images/simone-cerruti.webp')
        ->toContain('images/simone-cerruti-600.webp')
        ->toContain('portrait-showcase-logo-wave')
        ->toContain('portrait-showcase-cursor-glow');
});

it('forwards extra attributes to the wrapper element', function (): void {
    expect(Blade::render('<x-portrait-showcase alternative-text="A friendly portrait" data-cursor="Hi" />'))
        ->toContain('data-cursor="Hi"');
});
