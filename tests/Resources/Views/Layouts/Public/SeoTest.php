<?php

declare(strict_types=1);

use App\DataTransferObjects\SeoData;

it('renders every section present on the SeoData', function (): void {
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

it('always emits a title, falling back to the app name', function (?string $title): void {
    $html = view('layouts.public.seo', ['seo_data' => new SeoData(title: $title)])->render();

    expect($html)
        ->toContain('<title>'.config('app.name').'</title>')
        ->not->toContain('<title></title>');
})->with([
    'null title' => [null],
    'empty title' => [''],
]);

it('omits sections that are not present on the SeoData', function (): void {
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

it('renders nothing when no SeoData is provided', function (): void {
    expect(view('layouts.public.seo', ['seo_data' => null])->render())
        ->not->toContain('<title')
        ->not->toContain('<meta')
        ->not->toContain('<link');
});

it('escapes JSON-LD so it cannot break out of the script tag', function (): void {
    $html = view('layouts.public.seo', ['seo_data' => new SeoData(
        json_ld: [['@type' => 'Article', 'headline' => '</script><script>alert(1)']],
    )])->render();

    expect($html)
        ->not->toContain('<script>alert(1)')
        ->toContain('alert(1)');
});
