<?php

declare(strict_types=1);

use App\Enums\DeviceType;
use App\Enums\VisitMediumType;
use App\Enums\VisitSourceType;
use App\Models\Campaign;
use App\Models\PageView;
use App\Models\VisitSession;
use Illuminate\Support\Facades\Route;

use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\withCookie;
use function Pest\Laravel\withHeaders;

beforeEach(function (): void {
    Route::middleware('web')->group(function (): void {
        Route::get('/_test_track/landing', fn () => 'ok')->name('test_track.landing');
        Route::get('/_test_track/second', fn () => 'ok')->name('test_track.second');
        Route::get('/_test_track/redirect', fn () => redirect('/_test_track/landing'));
        Route::post('/_test_track/post', fn () => 'ok');
    });
});

describe('skip rules', function (): void {
    it('does not track non-GET requests', function (): void {
        post('/_test_track/post');

        expect(VisitSession::query()->count())->toBe(0)
            ->and(PageView::query()->count())->toBe(0);
    });

    it('does not track redirects', function (): void {
        get('/_test_track/redirect');

        expect(VisitSession::query()->count())->toBe(0);
    });

    it('does not track admin routes', function (): void {
        get('/admin');

        expect(VisitSession::query()->count())->toBe(0);
    });

    it('tracks a plain GET request', function (): void {
        expect(VisitSession::query()->count())->toBe(0);

        get('/_test_track/landing');

        expect(VisitSession::query()->count())->toBe(1);
    });
});

describe('new session', function (): void {
    it('creates a session and page view on first visit', function (): void {
        expect(VisitSession::query()->count())->toBe(0)
            ->and(PageView::query()->count())->toBe(0);

        get('/_test_track/landing');

        $session = VisitSession::query()->firstOrFail();
        $page_view = PageView::query()->firstOrFail();

        expect($session->source)->toBe(VisitSourceType::DIRECT->value)
            ->and($session->landing_path)->toBe('/_test_track/landing')
            ->and($session->landing_route_name)->toBe('test_track.landing')
            ->and($session->pageview_count)->toBe(1)
            ->and($page_view->visit_session_id)->toBe($session->id)
            ->and($page_view->route_name)->toBe('test_track.landing');
    });

    it('captures utm parameters and matches an active campaign', function (): void {
        $campaign = Campaign::factory()->create([
            'slug' => 'insta-lancio',
            'source' => 'instagram',
            'medium' => VisitMediumType::SOCIAL->value,
        ]);

        expect(VisitSession::query()->count())->toBe(0);

        get('/_test_track/landing?utm_source=ig&utm_medium=social&utm_campaign=insta-lancio');

        $session = VisitSession::query()->firstOrFail();

        expect($session->source)->toBe('instagram')
            ->and($session->medium)->toBe(VisitMediumType::SOCIAL->value)
            ->and($session->campaign_id)->toBe($campaign->id)
            ->and($session->utm_source)->toBe('ig')
            ->and($session->utm_campaign)->toBe('insta-lancio');
    });

    it('puts the social platform in source and "social" in medium for Instagram referrers', function (): void {
        withHeaders(['referer' => 'https://www.instagram.com/profile']);
        get('/_test_track/landing');

        $session = VisitSession::query()->firstOrFail();

        expect($session->source)->toBe('instagram')
            ->and($session->medium)->toBe(VisitMediumType::SOCIAL->value)
            ->and($session->referrer_host)->toBe('www.instagram.com');
    });

    it('records CF-IPCountry header regardless of consent', function (): void {
        withHeaders(['CF-IPCountry' => 'IT']);
        get('/_test_track/landing');

        expect(VisitSession::query()->firstOrFail()->country)->toBe('IT');
    });
});

describe('consent gating', function (): void {
    it('omits IP, user agent, visitor_id without analytics consent', function (): void {
        withHeaders(getBrowserRequestHeaders());
        get('/_test_track/landing');

        $session = VisitSession::query()->firstOrFail();

        expect($session->consent_analytics)->toBeFalse()
            ->and($session->ip)->toBeNull()
            ->and($session->user_agent)->toBeNull()
            ->and($session->visitor_id)->toBeNull();
    });

    it('classifies device type even without analytics consent so bots can be filtered', function (): void {
        withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)']);
        get('/_test_track/landing');

        $session = VisitSession::query()->firstOrFail();

        expect($session->consent_analytics)->toBeFalse()
            ->and($session->user_agent)->toBeNull()
            ->and($session->device_type)->toBe(DeviceType::BOT);
    });

    it('captures IP, user agent, device, visitor_id with analytics consent', function (): void {
        withCookie('cookie_consent', getAnalyticsConsentCookieValue());
        withHeaders(getBrowserRequestHeaders());
        get('/_test_track/landing');

        $session = VisitSession::query()->firstOrFail();

        expect($session->consent_analytics)->toBeTrue()
            ->and($session->ip)->not->toBeNull()
            ->and($session->user_agent)->toContain('Windows')
            ->and($session->device_type)->toBe(DeviceType::DESKTOP)
            ->and($session->bot_score)->toBe(0)
            ->and($session->visitor_id)->not->toBeNull();
    });
});

describe('bot detection', function (): void {
    it('reclassifies a forged browser User-Agent as a bot from missing header signals', function (): void {
        withHeaders(getAutomatedRequestHeaders());
        get('/_test_track/landing');

        $session = VisitSession::query()->firstOrFail();

        expect($session->device_type)->toBe(DeviceType::BOT)
            ->and($session->bot_score)->toBeGreaterThanOrEqual(config()->integer('analytics.bot_detection.threshold'));
    });

    it('keeps a genuine browser as its real device with a zero bot score', function (): void {
        withHeaders(getBrowserRequestHeaders());
        get('/_test_track/landing');

        $session = VisitSession::query()->firstOrFail();

        expect($session->device_type)->toBe(DeviceType::DESKTOP)
            ->and($session->bot_score)->toBe(0);
    });

    it('escalates a reused session to bot when a later request looks automated', function (): void {
        withHeaders(getBrowserRequestHeaders());
        get('/_test_track/landing');

        $session = VisitSession::query()->firstOrFail();

        expect($session->device_type)->toBe(DeviceType::DESKTOP);

        withCookie('vs_id', $session->id);
        withHeaders(getAutomatedRequestHeaders());
        get('/_test_track/second');

        $session->refresh();

        expect(VisitSession::query()->count())->toBe(1)
            ->and($session->device_type)->toBe(DeviceType::BOT)
            ->and($session->bot_score)->toBeGreaterThanOrEqual(config()->integer('analytics.bot_detection.threshold'));
    });

    it('never clears a bot classification on a later genuine-looking request', function (): void {
        withHeaders(getAutomatedRequestHeaders());
        get('/_test_track/landing');

        $session = VisitSession::query()->firstOrFail();
        $bot_score = $session->bot_score;

        expect($session->device_type)->toBe(DeviceType::BOT);

        withCookie('vs_id', $session->id);
        withHeaders(getBrowserRequestHeaders());
        get('/_test_track/second');

        $session->refresh();

        expect($session->device_type)->toBe(DeviceType::BOT)
            ->and($session->bot_score)->toBe($bot_score);
    });
});

describe('shared instance state', function (): void {
    it('does not bleed request state into a subsequent untracked request', function (): void {
        get('/_test_track/landing');
        post('/_test_track/post');

        expect(VisitSession::query()->count())->toBe(1)
            ->and(PageView::query()->count())->toBe(1);
    });
});

describe('cookie queueing', function (): void {
    it('queues the session cookie on every new session', function (): void {
        $response = get('/_test_track/landing');

        expect(collect($response->headers->getCookies())->map(fn ($cookie): string => $cookie->getName()))
            ->toContain('vs_id');
    });

    it('does not queue the visitor cookie without consent', function (): void {
        $response = get('/_test_track/landing');

        expect(collect($response->headers->getCookies())->map(fn ($cookie): string => $cookie->getName()))
            ->not->toContain('v_id');
    });

    it('queues the visitor cookie with consent', function (): void {
        withCookie('cookie_consent', getAnalyticsConsentCookieValue());
        $response = get('/_test_track/landing');

        expect(collect($response->headers->getCookies())->map(fn ($cookie): string => $cookie->getName()))
            ->toContain('v_id');
    });
});

describe('session reuse', function (): void {
    it('reuses an active session within the window and increments pageview_count', function (): void {
        get('/_test_track/landing');

        $session = VisitSession::query()->firstOrFail();

        expect($session->pageview_count)->toBe(1);

        withCookie('vs_id', $session->id);
        get('/_test_track/second');

        $session->refresh();

        expect(VisitSession::query()->count())->toBe(1)
            ->and($session->pageview_count)->toBe(2)
            ->and(PageView::query()->count())->toBe(2);
    });

    it('creates a new session when the previous one is expired', function (): void {
        $expired = VisitSession::factory()->expired()->create();

        expect(VisitSession::query()->count())->toBe(1);

        withCookie('vs_id', $expired->id);
        get('/_test_track/landing');

        expect(VisitSession::query()->count())->toBe(2);
    });
});

describe('session recovery', function (): void {
    it('opens a fresh session for a missing-session cookie and reuses it on the next request', function (): void {
        $missing_id = fake()->uuid();

        expect(VisitSession::query()->count())->toBe(0);

        withCookie('vs_id', $missing_id);
        $response = get('/_test_track/landing');

        $session = VisitSession::query()->firstOrFail();
        $queued_cookies = collect($response->headers->getCookies())->map(fn ($cookie): string => $cookie->getName());

        expect(VisitSession::query()->count())->toBe(1)
            ->and($session->id)->not->toBe($missing_id)
            ->and($queued_cookies)->toContain('vs_id');

        withCookie('vs_id', $session->id);
        get('/_test_track/second');

        $session->refresh();

        expect(VisitSession::query()->count())->toBe(1)
            ->and($session->pageview_count)->toBe(2);
    });

    it('preserves the visitor_id from the v_id cookie when recovering from a stale session cookie', function (): void {
        $visitor_id = fake()->uuid();

        withCookie('cookie_consent', getAnalyticsConsentCookieValue());
        withCookie('v_id', $visitor_id);
        withCookie('vs_id', fake()->uuid());
        get('/_test_track/landing');

        $session = VisitSession::query()->firstOrFail();

        expect($session->consent_analytics)->toBeTrue()
            ->and($session->visitor_id)->toBe($visitor_id);
    });
});
