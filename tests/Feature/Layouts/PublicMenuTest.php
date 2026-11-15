<?php

declare(strict_types=1);

use Illuminate\Testing\TestResponse;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;

beforeEach(fn () => $this->withoutVite());

function getPageWithoutLocaleRedirects(string $route_name): TestResponse {
    return test()->withoutMiddleware([
        LaravelLocalizationRedirectFilter::class,
        LocaleSessionRedirect::class,
    ])->get(route($route_name));
}

it('renders every primary navigation entry in order', function () {
    getPageWithoutLocaleRedirects('about')
        ->assertOk()
        ->assertSeeInOrder([
            'cta-nav-home',
            'cta-nav-projects',
            'cta-nav-how_i_work',
            'cta-nav-services',
            'cta-nav-about',
            'cta-nav-blog',
            'cta-nav-contacts',
        ]);
});

it('renders the mobile menu with the same entries and the call to action', function () {
    getPageWithoutLocaleRedirects('about')
        ->assertOk()
        ->assertSeeInOrder([
            'cta-nav-home-mobile',
            'cta-nav-projects-mobile',
            'cta-nav-how_i_work-mobile',
            'cta-nav-services-mobile',
            'cta-nav-about-mobile',
            'cta-nav-blog-mobile',
            'cta-nav-contacts-mobile',
            'cta-nav-cta-mobile',
        ]);
});

it('links the services navigation entry to the services page', function () {
    getPageWithoutLocaleRedirects('about')
        ->assertOk()
        ->assertSee('data-pan="cta-nav-services"', escape: false)
        ->assertSee(route('services'), escape: false)
        ->assertSee(__('Services'));
});

it('marks the current page in the primary navigation', function () {
    getPageWithoutLocaleRedirects('about')
        ->assertOk()
        ->assertSee('class="is-current" aria-current="page"', escape: false);
});

it('offers every supported locale in the language switcher', function () {
    getPageWithoutLocaleRedirects('about')
        ->assertOk()
        ->assertSee('hreflang="it"', escape: false)
        ->assertSee('hreflang="en"', escape: false);
});
