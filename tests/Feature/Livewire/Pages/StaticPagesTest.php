<?php

declare(strict_types=1);

use App\Livewire\Pages\About;
use App\Livewire\Pages\Contacts;
use App\Livewire\Pages\CookiePolicy;
use App\Livewire\Pages\HowIWork;
use App\Livewire\Pages\PrivacyPolicy;
use App\Livewire\Pages\Services;
use App\Livewire\Pages\ServicesManagementErpCrm;
use App\Livewire\Pages\TermsAndConditions;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;

use function Pest\Livewire\livewire;

beforeEach(fn () => $this->withoutVite());

it('renders the static pages', function (string $component) {
    livewire($component)->assertOk();
})->with([
    'about' => [About::class],
    'how I work' => [HowIWork::class],
    'services' => [Services::class],
    'services management erp crm' => [ServicesManagementErpCrm::class],
    'contacts' => [Contacts::class],
    'cookie policy' => [CookiePolicy::class],
    'privacy policy' => [PrivacyPolicy::class],
    'terms and conditions' => [TermsAndConditions::class],
]);

it('keeps the legal pages out of the index with a noindex robots tag', function (string $route) {
    // Skip mcamara's locale redirects so the unprefixed route resolves directly,
    // mirroring the SeoComposer rendered-page test.
    $response = $this->withoutMiddleware([
        LaravelLocalizationRedirectFilter::class,
        LocaleSessionRedirect::class,
    ])->get(route($route));

    $response->assertOk()
        ->assertSee('<meta name="robots" content="noindex">', escape: false);
})->with([
    'cookie policy' => ['cookie_policy'],
    'privacy policy' => ['privacy_policy'],
    'terms and conditions' => ['terms_and_conditions'],
]);

it('leaves non-legal pages indexable', function (string $route) {
    $response = $this->withoutMiddleware([
        LaravelLocalizationRedirectFilter::class,
        LocaleSessionRedirect::class,
    ])->get(route($route));

    $response->assertOk()
        ->assertDontSee('name="robots"', escape: false);
})->with([
    'about' => ['about'],
    'contacts' => ['contacts'],
    'services' => ['services'],
    'services management erp crm' => ['services.management_erp_crm'],
]);
