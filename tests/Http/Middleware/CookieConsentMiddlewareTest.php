<?php

declare(strict_types=1);

use App\Http\Middleware\CookieConsentMiddleware;
use Illuminate\Http\Request;

it('shares a no-consent default when no cookie is present', function () {
    $request = Request::create('/');

    (new CookieConsentMiddleware)->handle($request, fn () => response('ok'));

    expect(view()->shared('cookieConsent'))->toMatchArray([
        'given' => false,
        'necessary' => true,
        'analytics' => false,
        'functional' => false,
        'marketing' => false,
    ]);
});

it('parses a valid consent cookie', function () {
    $request = Request::create('/');
    $request->cookies->set('cookie_consent', json_encode([
        'version' => 1,
        'analytics' => true,
        'functional' => false,
        'marketing' => true,
    ]));

    (new CookieConsentMiddleware)->handle($request, fn () => response('ok'));

    expect(view()->shared('cookieConsent'))->toMatchArray([
        'given' => true,
        'necessary' => true,
        'analytics' => true,
        'functional' => false,
        'marketing' => true,
    ]);
});

it('ignores a cookie without a version marker', function () {
    $request = Request::create('/');
    $request->cookies->set('cookie_consent', json_encode(['analytics' => true]));

    (new CookieConsentMiddleware)->handle($request, fn () => response('ok'));

    expect(view()->shared('cookieConsent'))->toMatchArray(['given' => false]);
});

it('ignores a malformed cookie without throwing', function () {
    $request = Request::create('/');
    $request->cookies->set('cookie_consent', 'not-valid-json{');

    (new CookieConsentMiddleware)->handle($request, fn () => response('ok'));

    expect(view()->shared('cookieConsent'))->toMatchArray(['given' => false]);
});
