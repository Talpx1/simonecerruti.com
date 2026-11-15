<?php

declare(strict_types=1);

use App\Enums\DeviceType;
use App\Enums\VisitMediumType;
use App\Enums\VisitSourceType;
use App\Models\Campaign;
use App\Models\PageView;
use App\Models\VisitSession;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Route::middleware('web')->group(function () {
        Route::get('/_test_track/landing', fn () => 'ok')->name('test_track.landing');
        Route::get('/_test_track/second', fn () => 'ok')->name('test_track.second');
        Route::get('/_test_track/redirect', fn () => redirect('/_test_track/landing'));
        Route::post('/_test_track/post', fn () => 'ok');
    });
});

describe('skip rules', function () {
    it('does not track non-GET requests', function () {
        $this->post('/_test_track/post');

        expect(VisitSession::query()->count())->toBe(0)
            ->and(PageView::query()->count())->toBe(0);
    });

    it('does not track redirects', function () {
        $this->get('/_test_track/redirect');

        expect(VisitSession::query()->count())->toBe(0);
    });

    it('does not track admin routes', function () {
        $this->get('/admin');

        expect(VisitSession::query()->count())->toBe(0);
    });
});

describe('new session', function () {
    it('creates a session and page view on first visit', function () {
        $this->get('/_test_track/landing');

        $session = VisitSession::query()->first();
        $page_view = PageView::query()->first();

        expect($session)->not->toBeNull()
            ->and($session->source)->toBe(VisitSourceType::DIRECT->value)
            ->and($session->landing_path)->toBe('/_test_track/landing')
            ->and($session->landing_route_name)->toBe('test_track.landing')
            ->and($session->pageview_count)->toBe(1)
            ->and($page_view->visit_session_id)->toBe($session->id)
            ->and($page_view->route_name)->toBe('test_track.landing');
    });

    it('captures utm parameters and matches an active campaign', function () {
        $campaign = Campaign::factory()->create([
            'slug' => 'insta-lancio',
            'source' => 'instagram',
            'medium' => VisitMediumType::SOCIAL->value,
        ]);

        $this->get('/_test_track/landing?utm_source=ig&utm_medium=social&utm_campaign=insta-lancio');

        $session = VisitSession::query()->first();

        expect($session->source)->toBe('instagram')
            ->and($session->medium)->toBe(VisitMediumType::SOCIAL->value)
            ->and($session->campaign_id)->toBe($campaign->id)
            ->and($session->utm_source)->toBe('ig')
            ->and($session->utm_campaign)->toBe('insta-lancio');
    });

    it('puts the social platform in source and "social" in medium for Instagram referrers', function () {
        $this->withHeaders([
            'referer' => 'https://www.instagram.com/profile',
        ])->get('/_test_track/landing');

        $session = VisitSession::query()->first();

        expect($session->source)->toBe('instagram')
            ->and($session->medium)->toBe(VisitMediumType::SOCIAL->value)
            ->and($session->referrer_host)->toBe('www.instagram.com');
    });

    it('records CF-IPCountry header regardless of consent', function () {
        $this->withHeaders(['CF-IPCountry' => 'IT'])->get('/_test_track/landing');

        expect(VisitSession::query()->first()->country)->toBe('IT');
    });
});

describe('consent gating', function () {
    it('omits IP, user agent, visitor_id without analytics consent', function () {
        $this->withHeaders(['User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X) Chrome/124.0'])
            ->get('/_test_track/landing');

        $session = VisitSession::query()->first();

        expect($session->consent_analytics)->toBeFalse()
            ->and($session->ip)->toBeNull()
            ->and($session->user_agent)->toBeNull()
            ->and($session->visitor_id)->toBeNull();
    });

    it('classifies device type even without analytics consent so bots can be filtered', function () {
        $this->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)'])
            ->get('/_test_track/landing');

        $session = VisitSession::query()->first();

        expect($session->consent_analytics)->toBeFalse()
            ->and($session->user_agent)->toBeNull()
            ->and($session->device_type)->toBe(DeviceType::BOT);
    });

    it('captures IP, user agent, device, visitor_id with analytics consent', function () {
        $consent = ['version' => 1, 'analytics' => true];

        $this
            ->withCookie('cookie_consent', json_encode($consent))
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X) Chrome/124.0',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.9',
                'Accept-Encoding' => 'gzip, deflate, br',
                'Sec-Fetch-Site' => 'none',
            ])
            ->get('/_test_track/landing');

        $session = VisitSession::query()->first();

        expect($session->consent_analytics)->toBeTrue()
            ->and($session->ip)->not->toBeNull()
            ->and($session->user_agent)->toContain('Macintosh')
            ->and($session->device_type)->toBe(DeviceType::DESKTOP)
            ->and($session->bot_score)->toBe(0)
            ->and($session->visitor_id)->not->toBeNull();
    });
});

describe('bot detection', function () {
    it('reclassifies a forged browser User-Agent as a bot from missing header signals', function () {
        // A real desktop Chrome UA, but the request omits the Accept and
        // Accept-Language headers a genuine browser always sends and carries no
        // Sec-Fetch metadata: the header score crosses the threshold and the
        // visit is reclassified from desktop to bot.
        $this->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
            'Accept' => '',
            'Accept-Language' => '',
        ])->get('/_test_track/landing');

        $session = VisitSession::query()->first();

        expect($session->device_type)->toBe(DeviceType::BOT)
            ->and($session->bot_score)->toBeGreaterThanOrEqual(config()->integer('analytics.bot_detection.threshold'));
    });

    it('keeps a genuine browser as its real device with a zero bot score', function () {
        $this->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language' => 'it-IT,it;q=0.9,en;q=0.8',
            'Accept-Encoding' => 'gzip, deflate, br',
            'Sec-Fetch-Site' => 'none',
        ])->get('/_test_track/landing');

        $session = VisitSession::query()->first();

        expect($session->device_type)->toBe(DeviceType::DESKTOP)
            ->and($session->bot_score)->toBe(0);
    });

    it('escalates a reused session to bot when a later request looks automated', function () {
        $chrome = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36';

        // First request is a genuine browser: session opens as desktop, score 0.
        $this->withHeaders([
            'User-Agent' => $chrome,
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language' => 'it-IT,it;q=0.9',
            'Accept-Encoding' => 'gzip, deflate, br',
            'Sec-Fetch-Site' => 'none',
        ])->get('/_test_track/landing');

        $session = VisitSession::query()->first();
        expect($session->device_type)->toBe(DeviceType::DESKTOP);

        // Same session cookie, but now a header-less automated request.
        $this->withCookie('vs_id', $session->id)
            ->withHeaders(['User-Agent' => $chrome, 'Accept' => '', 'Accept-Language' => ''])
            ->get('/_test_track/second');

        $session->refresh();

        expect(VisitSession::query()->count())->toBe(1)
            ->and($session->device_type)->toBe(DeviceType::BOT)
            ->and($session->bot_score)->toBeGreaterThanOrEqual(config()->integer('analytics.bot_detection.threshold'));
    });

    it('never clears a bot classification on a later genuine-looking request', function () {
        $chrome = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36';

        // First request looks automated: session is flagged bot.
        $this->withHeaders(['User-Agent' => $chrome, 'Accept' => '', 'Accept-Language' => ''])
            ->get('/_test_track/landing');

        $session = VisitSession::query()->first();
        expect($session->device_type)->toBe(DeviceType::BOT);
        $bot_score = $session->bot_score;

        // A later genuine request on the same session must not de-escalate it.
        $this->withCookie('vs_id', $session->id)->withHeaders([
            'User-Agent' => $chrome,
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language' => 'it-IT,it;q=0.9',
            'Accept-Encoding' => 'gzip, deflate, br',
            'Sec-Fetch-Site' => 'none',
        ])->get('/_test_track/second');

        $session->refresh();

        expect($session->device_type)->toBe(DeviceType::BOT)
            ->and($session->bot_score)->toBe($bot_score);
    });
});

describe('shared instance state', function () {
    it('does not bleed request state into a subsequent untracked request', function () {
        // TrackVisit is a singleton, so handle() and terminate() share one
        // instance across requests in this test. handle() must reset its state
        // each request or the previous request's state would leak into the next
        // request's terminate() and record a phantom session.
        $this->get('/_test_track/landing');
        $this->post('/_test_track/post');

        expect(VisitSession::query()->count())->toBe(1)
            ->and(PageView::query()->count())->toBe(1);
    });
});

describe('cookie queueing', function () {
    it('queues the session cookie on every new session', function () {
        $response = $this->get('/_test_track/landing');

        $cookies = collect($response->headers->getCookies())
            ->keyBy(fn ($c) => $c->getName());

        expect($cookies)->toHaveKey('vs_id');
    });

    it('does not queue the visitor cookie without consent', function () {
        $response = $this->get('/_test_track/landing');

        $names = collect($response->headers->getCookies())->map(fn ($c) => $c->getName());

        expect($names)->not->toContain('v_id');
    });

    it('queues the visitor cookie with consent', function () {
        $consent = ['version' => 1, 'analytics' => true];

        $response = $this->withCookie('cookie_consent', json_encode($consent))
            ->get('/_test_track/landing');

        $names = collect($response->headers->getCookies())->map(fn ($c) => $c->getName());

        expect($names)->toContain('v_id');
    });
});

describe('session reuse', function () {
    it('reuses an active session within the window and increments pageview_count', function () {
        $this->get('/_test_track/landing');

        $session = VisitSession::query()->first();
        $cookie = $session->id;

        $this->withCookie('vs_id', $cookie)->get('/_test_track/second');

        $session->refresh();

        expect(VisitSession::query()->count())->toBe(1)
            ->and($session->pageview_count)->toBe(2)
            ->and(PageView::query()->count())->toBe(2);
    });

    it('creates a new session when the previous one is expired', function () {
        $expired = VisitSession::factory()->expired()->create();

        $this->withCookie('vs_id', $expired->id)->get('/_test_track/landing');

        expect(VisitSession::query()->count())->toBe(2);
    });
});

describe('session recovery', function () {
    it('opens a fresh session for a missing-session cookie and reuses it on the next request', function () {
        $missing_id = fake()->uuid();

        // A cookie pointing at a session that no longer exists (pruned row,
        // schema reset) must open a fresh session under a NEW id and queue its
        // cookie, not silently mint an orphan under the stale id.
        $response = $this->withCookie('vs_id', $missing_id)->get('/_test_track/landing');

        $session = VisitSession::query()->first();
        $queued_cookies = collect($response->headers->getCookies())->map(fn ($c) => $c->getName());

        expect(VisitSession::query()->count())->toBe(1)
            ->and($session->id)->not->toBe($missing_id)
            ->and($queued_cookies)->toContain('vs_id');

        // The browser follows the re-queued cookie: the next request reuses the
        // session instead of minting another orphan (the bug being fixed).
        $this->withCookie('vs_id', $session->id)->get('/_test_track/second');

        $session->refresh();

        expect(VisitSession::query()->count())->toBe(1)
            ->and($session->pageview_count)->toBe(2);
    });

    it('preserves the visitor_id from the v_id cookie when recovering from a stale session cookie', function () {
        $consent = ['version' => 1, 'analytics' => true];
        $visitor_id = fake()->uuid();

        $this->withCookie('cookie_consent', json_encode($consent))
            ->withCookie('v_id', $visitor_id)
            ->withCookie('vs_id', fake()->uuid())
            ->get('/_test_track/landing');

        $session = VisitSession::query()->first();

        expect($session->consent_analytics)->toBeTrue()
            ->and($session->visitor_id)->toBe($visitor_id);
    });
});
