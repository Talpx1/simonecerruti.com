<?php

declare(strict_types=1);

use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;

beforeEach(fn () => $this->withoutVite());

it('marks the page hero title so its letters animate on load', function (string $route) {
    $this->withoutMiddleware([
        LaravelLocalizationRedirectFilter::class,
        LocaleSessionRedirect::class,
    ])->get(route($route))
        ->assertOk()
        ->assertSee('data-variable-letters', escape: false);
})->with([
    'projects' => ['projects'],
    'blog' => ['blog'],
    'contacts' => ['contacts'],
    'services hub' => ['services'],
    'services management' => ['services.management_erp_crm'],
    'services web' => ['services.web_ecommerce_platforms'],
    'services consulting' => ['services.consulting_and_seo'],
]);

it('marks content with the reveal class so it animates in on scroll', function (string $route) {
    $this->withoutMiddleware([
        LaravelLocalizationRedirectFilter::class,
        LocaleSessionRedirect::class,
    ])->get(route($route))
        ->assertOk()
        ->assertSee('class="section-title reveal', escape: false);
})->with([
    'services management' => ['services.management_erp_crm'],
    'services web' => ['services.web_ecommerce_platforms'],
    'services consulting' => ['services.consulting_and_seo'],
]);
