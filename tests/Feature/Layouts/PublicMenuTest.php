<?php

declare(strict_types=1);

use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;

beforeEach(fn () => $this->withoutVite());

// Skip mcamara's locale redirects so the unprefixed route renders the full
// layout (and its menu) directly, mirroring the StaticPagesTest assertions.
it('renders every primary navigation entry in order', function () {
    $this->withoutMiddleware([
        LaravelLocalizationRedirectFilter::class,
        LocaleSessionRedirect::class,
    ])->get(route('about'))
        ->assertOk()
        ->assertSeeInOrder([
            'cta-nav-home',
            'cta-nav-about',
            'cta-nav-services',
            'cta-nav-projects',
            'cta-nav-how_i_work',
            'cta-nav-contacts',
            'cta-nav-blog',
        ]);
});

it('links the services navigation entry to the services page', function () {
    $this->withoutMiddleware([
        LaravelLocalizationRedirectFilter::class,
        LocaleSessionRedirect::class,
    ])->get(route('about'))
        ->assertOk()
        ->assertSee('data-pan="cta-nav-services"', escape: false)
        ->assertSee(route('services'), escape: false)
        ->assertSee(__('Services'));
});
