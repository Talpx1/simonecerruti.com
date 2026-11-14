<?php

declare(strict_types=1);

use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;

beforeEach(fn () => $this->withoutVite());

it('marks content with the rv class so it animates in on scroll', function (string $route) {
    $this->withoutMiddleware([
        LaravelLocalizationRedirectFilter::class,
        LocaleSessionRedirect::class,
    ])->get(route($route))
        ->assertOk()
        ->assertSee('rv', escape: false);
})->with([
    'projects' => ['projects'],
    'blog' => ['blog'],
    'contacts' => ['contacts'],
    'services hub' => ['services'],
    'services management' => ['services.management_erp_crm'],
    'services web' => ['services.web_ecommerce_platforms'],
    'services consulting' => ['services.consulting_and_seo'],
]);
