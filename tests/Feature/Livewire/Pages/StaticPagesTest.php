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

it('renders the legal pages with the switcher, the side index and the reading bar', function (string $component, string $current_key) {
    livewire($component)
        ->assertOk()
        ->assertSeeHtml('class="legal-h1"')
        ->assertSeeHtml('data-effect="legal"')
        ->assertSeeHtml('class="article-toc"')
        ->assertSeeHtml('class="article-progress"')
        ->assertSeeHtml('class="is-current" aria-current="page">'.__($current_key).'</a>')
        ->assertSeeHtml('href="'.route('privacy_policy').'"')
        ->assertSeeHtml('href="'.route('cookie_policy').'"')
        ->assertSeeHtml('href="'.route('terms_and_conditions').'"');
})->with([
    'privacy policy' => [PrivacyPolicy::class, 'Privacy'],
    'cookie policy' => [CookiePolicy::class, 'Cookies'],
    'terms and conditions' => [TermsAndConditions::class, 'Terms'],
]);

it('anchors every side index entry to a heading of the legal text', function (string $component) {
    $html = livewire($component)->html();

    preg_match_all('/<a href="#([a-z0-9-]+)">/', $html, $anchors);

    expect($anchors[1])->not->toBeEmpty();

    foreach ($anchors[1] as $anchor) {
        expect($html)->toContain('<h2 id="'.$anchor.'">');
    }
})->with([
    'privacy policy' => [PrivacyPolicy::class],
    'cookie policy' => [CookiePolicy::class],
    'terms and conditions' => [TermsAndConditions::class],
]);

it('reads the legal contact email from the company contacts config', function (string $component) {
    livewire($component)->assertSeeHtml('mailto:'.config()->string('company.contacts.email'));
})->with([
    'privacy policy' => [PrivacyPolicy::class],
    'cookie policy' => [CookiePolicy::class],
    'terms and conditions' => [TermsAndConditions::class],
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

it('renders the about page with the portrait, the paint canvas and the hobby accordion', function () {
    livewire(About::class)
        ->assertOk()
        ->assertSeeHtml('images/simone-cerruti.jpg')
        ->assertSeeHtml('data-effect="portrait-showcase"')
        ->assertSeeHtml('id="pixel-paint-canvas"')
        ->assertSeeHtml('data-effect="about"')
        ->assertSeeHtml('class="accordion-item"')
        ->assertSeeHtml('data-pan="cta-about-method"')
        ->assertSeeHtml('data-pan="cta-about-contacts"');
});

it('renders the how I work page with the interactive demos and their config', function () {
    livewire(HowIWork::class)
        ->assertOk()
        ->assertSeeHtml('data-effect="how-i-work"')
        ->assertSeeHtml('id="method-fit-section"')
        ->assertSeeHtml('class="page-effect-config"')
        ->assertSee(__('Orders after 5 pm'))
        ->assertSeeHtml('data-pan="cta-method-contacts"')
        ->assertSeeHtml('data-pan="cta-method-services"')
        ->assertSeeHtml('data-pan="cta-method-projects"');
});
