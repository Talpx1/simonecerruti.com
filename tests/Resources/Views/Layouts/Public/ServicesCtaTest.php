<?php

declare(strict_types=1);

use App\Enums\TagTypes;
use App\Livewire\Pages\Home;
use App\Models\Tag;

use function Pest\Livewire\livewire;

describe('services CTA', function (): void {
    it('is exposed on the home page', function (): void {
        Tag::findOrCreate(['practical', 'technical'], TagTypes::BLOG_CATEGORY->value);

        livewire(Home::class)
            ->assertOk()
            ->assertSeeHtml('data-pan="cta-home-services"');
    });

    it('is exposed on the standalone pages', function (string $route, string $pan): void {
        getPageWithoutLocaleRedirects($route)
            ->assertOk()
            ->assertSee('data-pan="'.$pan.'"', escape: false);
    })->with([
        'how I work' => ['how_i_work', 'cta-method-services'],
    ]);

    it('is absent from pages that do not carry it', function (): void {
        getPageWithoutLocaleRedirects('contacts')
            ->assertOk()
            ->assertDontSee('data-pan="cta-method-services"', escape: false);
    });
});

it('keeps the refactored markup intact', function (string $route, string $needle): void {
    getPageWithoutLocaleRedirects($route)
        ->assertOk()
        ->assertSee(__($needle));
})->with([
    'how-i-work closing statement' => ['how_i_work', 'You do not need faster software. You need software that really works.'],
    'about partner statement' => ['about', 'I am not a supplier. I am the technical partner who learns how your business works before touching a line of code.'],
]);
