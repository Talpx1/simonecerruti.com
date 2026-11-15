<?php

declare(strict_types=1);

use App\Livewire\Pages\About;

use function Pest\Livewire\livewire;

it('renders the page with the portrait, the paint canvas and the hobby accordion', function (): void {
    livewire(About::class)
        ->assertOk()
        ->assertSeeHtml('images/about_protrait.webp')
        ->assertSeeHtml('id="pixel-paint-canvas"')
        ->assertSeeHtml('data-effect="about"')
        ->assertSeeHtml('class="accordion-item"')
        ->assertSeeHtml('data-pan="cta-about-method"')
        ->assertSeeHtml('data-pan="cta-about-contacts"');
});

it('leaves the page indexable', function (): void {
    assertPageIsIndexable('about');
});
