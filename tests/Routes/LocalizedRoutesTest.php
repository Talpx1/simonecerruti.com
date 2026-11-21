<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

use function Pest\Laravel\get;
use function Safe\putenv;

afterEach(function (): void {
    putenv('ROUTING_LOCALE');
});

it('serves every named public route under every supported locale', function (string $locale): void {
    loadRoutesForLocale($locale);

    $names = getLocalizedPublicRouteNames();

    expect($names)->not->toBeEmpty();

    foreach ($names as $name) {
        expect(Route::has($name))->toBeTrue()
            ->and(route($name))->toContain("/{$locale}");

        get(route($name))->assertOk();
    }
})->with(['en', 'it']);

it('does not serve a locale prefix that is not supported', function (): void {
    expect(array_keys(LaravelLocalization::getSupportedLocales()))->not->toContain('xx');

    get('/xx')->assertNotFound();
});
