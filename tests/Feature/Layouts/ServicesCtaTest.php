<?php

declare(strict_types=1);

use App\Enums\BlogCategories;
use App\Livewire\Pages\Home;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;

use function Pest\Livewire\livewire;

beforeEach(fn () => $this->withoutVite());

it('exposes a services CTA on the home page', function () {
    BlogCategories::sync();

    livewire(Home::class)
        ->assertOk()
        ->assertSeeHtml('data-pan="cta-home-services"');
});

it('exposes a services CTA on the standalone pages', function (string $route, string $pan) {
    $this->withoutMiddleware([
        LaravelLocalizationRedirectFilter::class,
        LocaleSessionRedirect::class,
    ])->get(route($route))
        ->assertOk()
        ->assertSee('data-pan="'.$pan.'"', escape: false);
})->with([
    'how I work' => ['how_i_work', 'cta-method-services'],
]);

it('keeps the refactored markup intact', function (string $route, string $needle) {
    $this->withoutMiddleware([
        LaravelLocalizationRedirectFilter::class,
        LocaleSessionRedirect::class,
    ])->get(route($route))
        ->assertOk()
        ->assertSee(__($needle));
})->with([
    'how-i-work closing statement' => ['how_i_work', 'You do not need faster software. You need software that really works.'],
    'about partner statement' => ['about', 'I am not a supplier. I am the technical partner who learns how your business works before touching a line of code.'],
]);
