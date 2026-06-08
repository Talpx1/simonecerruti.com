<?php

declare(strict_types=1);

use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;

beforeEach(fn () => $this->withoutVite());

it('marks content with data-reveal so it animates in on scroll', function (string $route) {
    $this->withoutMiddleware([
        LaravelLocalizationRedirectFilter::class,
        LocaleSessionRedirect::class,
    ])->get(route($route))
        ->assertOk()
        ->assertSee('data-reveal', escape: false);
})->with([
    'about' => ['about'],
    'how I work' => ['how_i_work'],
    'projects' => ['projects'],
    'blog' => ['blog'],
    'contacts' => ['contacts'],
]);
