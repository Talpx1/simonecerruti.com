<?php

declare(strict_types=1);

describe('primary navigation', function (): void {
    it('renders every primary navigation entry in order', function (): void {
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

    it('links the services navigation entry to the services page', function (): void {
        getPageWithoutLocaleRedirects('about')
            ->assertOk()
            ->assertSee('data-pan="cta-nav-services"', escape: false)
            ->assertSee(route('services'), escape: false)
            ->assertSee(__('Services'));
    });

    it('marks the current page in the primary navigation', function (): void {
        getPageWithoutLocaleRedirects('about')
            ->assertOk()
            ->assertSee('class="is-current" aria-current="page"', escape: false);
    });
});

describe('mobile menu', function (): void {
    it('renders the same entries and the call to action', function (): void {
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
});

describe('language switcher', function (): void {
    it('offers every supported locale', function (): void {
        getPageWithoutLocaleRedirects('about')
            ->assertOk()
            ->assertSee('hreflang="it"', escape: false)
            ->assertSee('hreflang="en"', escape: false);
    });
});
