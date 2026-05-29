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
            ->withHeaders(['User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X) Chrome/124.0'])
            ->get('/_test_track/landing');

        $session = VisitSession::query()->first();

        expect($session->consent_analytics)->toBeTrue()
            ->and($session->ip)->not->toBeNull()
            ->and($session->user_agent)->toContain('Macintosh')
            ->and($session->device_type)->toBe(DeviceType::DESKTOP)
            ->and($session->visitor_id)->not->toBeNull();
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
