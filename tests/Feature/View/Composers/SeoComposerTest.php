<?php

declare(strict_types=1);

use App\DataTransferObjects\SeoData;
use App\View\Composers\SeoComposer;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;

beforeEach(fn () => $this->withoutVite());

it('renders every section present on the SeoData', function () {
    $html = view('layouts.public.seo', ['seo_data' => new SeoData(
        title: 'My Page',
        description: 'A description',
        canonical: 'https://example.test/page',
        robots: 'noindex,nofollow',
        alternates: [
            ['hreflang' => 'en', 'href' => 'https://example.test/en/page'],
            ['hreflang' => 'it', 'href' => 'https://example.test/it/page'],
        ],
        open_graph: ['og:title' => 'My Page', 'og:type' => 'article'],
        twitter: ['twitter:card' => 'summary_large_image'],
        json_ld: [['@type' => 'Article', 'headline' => 'My Page']],
    )])->render();

    expect($html)
        ->toContain('<title>My Page</title>')
        ->toContain('<meta name="description" content="A description">')
        ->toContain('<meta name="robots" content="noindex,nofollow">')
        ->toContain('<link rel="canonical" href="https://example.test/page">')
        ->toContain('<link rel="alternate" hreflang="en" href="https://example.test/en/page">')
        ->toContain('<link rel="alternate" hreflang="it" href="https://example.test/it/page">')
        ->toContain('<meta property="og:title" content="My Page">')
        ->toContain('<meta property="og:type" content="article">')
        ->toContain('<meta name="twitter:card" content="summary_large_image">')
        ->toContain('<script type="application/ld+json">')
        ->toContain('"@context":"https://schema.org"')
        ->toContain('"@graph":[')
        ->toContain('"@type":"Article"');
});

it('always emits a title, falling back to the app name', function (?string $title) {
    $html = view('layouts.public.seo', ['seo_data' => new SeoData(title: $title)])->render();

    expect($html)
        ->toContain('<title>'.config('app.name').'</title>')
        ->not->toContain('<title></title>');
})->with([
    'null title' => [null],
    'empty title' => [''],
]);

it('omits sections that are not present on the SeoData', function () {
    $html = view('layouts.public.seo', ['seo_data' => new SeoData(title: 'Only a title')])->render();

    expect($html)
        ->toContain('<title>Only a title</title>')
        ->not->toContain('<meta name="description"')
        ->not->toContain('rel="canonical"')
        ->not->toContain('rel="alternate"')
        ->not->toContain('property="og:')
        ->not->toContain('name="twitter:')
        ->not->toContain('application/ld+json');
});

it('renders nothing when no SeoData is provided', function () {
    expect(view('layouts.public.seo', ['seo_data' => null])->render())
        ->not->toContain('<title')
        ->not->toContain('<meta')
        ->not->toContain('<link');
});

it('escapes JSON-LD so it cannot break out of the script tag', function () {
    $html = view('layouts.public.seo', ['seo_data' => new SeoData(
        json_ld: [['@type' => 'Article', 'headline' => '</script><script>alert(1)']],
    )])->render();

    // The angle brackets are hex-encoded by JSON_HEX_TAG, so the payload cannot
    // close the surrounding <script> tag.
    expect($html)
        ->not->toContain('<script>alert(1)')
        ->toContain('alert(1)');
});

it('keeps a page-provided SeoData and only prepends the sitewide schema nodes', function () {
    $provided = new SeoData(
        title: 'Provided by the page',
        json_ld: [['@type' => 'Article', 'headline' => 'Provided by the page']],
    );
    $view = view('layouts.public.index', ['seo_data' => $provided]);

    (new SeoComposer)->compose($view);

    $resolved = $view->getData()['seo_data'];

    expect($resolved)->not->toBe($provided)
        ->and($resolved->title)->toBe('Provided by the page')
        // Sitewide nodes (WebSite + identity) come first, the page node stays last.
        ->and($resolved->json_ld)->toHaveCount(3)
        ->and($resolved->json_ld[2])->toBe(['@type' => 'Article', 'headline' => 'Provided by the page'])
        ->and($resolved->json_ld[0]['@type'])->toBe('WebSite');
});

it('lets a page opt into robots directives via a layout variable', function () {
    $view = view('layouts.public.index', ['robots' => 'noindex']);

    (new SeoComposer)->compose($view);

    expect($view->getData()['seo_data']->robots)->toBe('noindex');
});

it('emits no robots directive by default', function () {
    $view = view('layouts.public.index');

    (new SeoComposer)->compose($view);

    expect($view->getData()['seo_data']->robots)->toBeNull();
});

it('emits one title, one canonical and one hreflang per locale on a rendered page', function () {
    // Skip mcamara's locale redirects: without a route cache the prefixed URLs
    // don't exist in tests, so we render the default locale at "/" directly.
    $response = $this->withoutMiddleware([
        LaravelLocalizationRedirectFilter::class,
        LocaleSessionRedirect::class,
    ])->get('/');

    $response->assertOk();
    $html = $response->getContent();

    expect(substr_count($html, '<title'))->toBe(1)
        ->and(substr_count($html, 'rel="canonical"'))->toBe(1)
        ->and(substr_count($html, '<link rel="alternate" hreflang='))->toBe(2)
        // Home opts out of the suffix, so the title is the bare app name.
        ->and($html)->toContain('<title>'.config('app.name').'</title>');
});
