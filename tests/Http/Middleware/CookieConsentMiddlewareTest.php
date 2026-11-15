<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Route;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\withCookie;

beforeEach(function (): void {
    Route::middleware('web')->get('/_test_cookie_consent', fn () => response()->json(view()->shared('cookieConsent')));
});

describe('guest', function (): void {
    it('shares a no-consent default when no cookie is present', function (): void {
        get('/_test_cookie_consent')
            ->assertOk()
            ->assertExactJson([
                'given' => false,
                'necessary' => true,
                'analytics' => false,
                'functional' => false,
                'marketing' => false,
            ]);
    });

    it('parses a valid consent cookie', function (): void {
        withCookie('cookie_consent', \Safe\json_encode([
            'version' => 1,
            'analytics' => true,
            'functional' => false,
            'marketing' => true,
        ]));

        get('/_test_cookie_consent')
            ->assertOk()
            ->assertExactJson([
                'given' => true,
                'necessary' => true,
                'analytics' => true,
                'functional' => false,
                'marketing' => true,
            ]);
    });

    it('ignores a cookie without a version marker', function (): void {
        withCookie('cookie_consent', \Safe\json_encode(['analytics' => true]));

        get('/_test_cookie_consent')
            ->assertOk()
            ->assertJson(['given' => false, 'analytics' => false]);
    });

    it('ignores a malformed cookie without throwing', function (): void {
        withCookie('cookie_consent', 'not-valid-json{');

        get('/_test_cookie_consent')
            ->assertOk()
            ->assertJson(['given' => false, 'analytics' => false]);
    });
});

describe('authenticated user', function (): void {
    it('shares a no-consent default when no cookie is present', function (): void {
        actingAs(User::factory()->create());

        get('/_test_cookie_consent')
            ->assertOk()
            ->assertJson(['given' => false, 'necessary' => true, 'analytics' => false]);
    });

    it('parses a valid consent cookie', function (): void {
        actingAs(User::factory()->create());
        withCookie('cookie_consent', getAnalyticsConsentCookieValue());

        get('/_test_cookie_consent')
            ->assertOk()
            ->assertJson(['given' => true, 'analytics' => true, 'marketing' => false]);
    });
});
