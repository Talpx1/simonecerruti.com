<?php

declare(strict_types=1);

use App\Data\SeoData;
use App\View\Composers\SeoComposer;

it('keeps a page-provided SeoData and only prepends the sitewide schema nodes', function (): void {
    $provided = new SeoData(
        title: 'Provided by the page',
        json_ld: [['@type' => 'Article', 'headline' => 'Provided by the page']],
    );
    $view = view('layouts.public.index', ['seo_data' => $provided]);

    (new SeoComposer)->compose($view);

    $resolved = $view->getData()['seo_data'];

    expect($resolved)->not->toBe($provided)
        ->and($resolved->title)->toBe('Provided by the page')
        ->and($resolved->json_ld)->toHaveCount(3)
        ->and($resolved->json_ld[2])->toBe(['@type' => 'Article', 'headline' => 'Provided by the page'])
        ->and($resolved->json_ld[0]['@type'])->toBe('WebSite');
});

it('lets a page opt into robots directives via a layout variable', function (): void {
    $view = view('layouts.public.index', ['robots' => 'noindex']);

    (new SeoComposer)->compose($view);

    expect($view->getData()['seo_data']->robots)->toBe('noindex');
});

it('emits no robots directive by default', function (): void {
    $view = view('layouts.public.index');

    (new SeoComposer)->compose($view);

    expect($view->getData()['seo_data']->robots)->toBeNull();
});

it('emits one title, one canonical and one hreflang per locale on a rendered page', function (): void {
    $html = (string) getUrlWithoutLocaleRedirects('/')->assertOk()->getContent();

    expect(\substr_count($html, '<title'))->toBe(1)
        ->and(\substr_count($html, 'rel="canonical"'))->toBe(1)
        ->and(\substr_count($html, '<link rel="alternate" hreflang='))->toBe(2)
        ->and($html)->toContain('<title>'.config('app.name').'</title>');
});
