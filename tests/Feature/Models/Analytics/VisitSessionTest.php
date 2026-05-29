<?php

declare(strict_types=1);

use App\Models\Campaign;
use App\Models\PageView;
use App\Models\VisitSession;
use Carbon\CarbonImmutable;

describe('primary key', function () {
    it('uses a uuid string id', function () {
        $session = VisitSession::factory()->create();

        expect($session->id)
            ->toBeString()
            ->and(preg_match('/^[0-9a-f-]{36}$/i', $session->id))->toBe(1);
    });
});

describe('casts', function () {
    it('casts timestamps and flags', function () {
        $session = VisitSession::factory()->withConsent()->create([
            'pageview_count' => '4',
        ]);

        expect($session->started_at)->toBeInstanceOf(CarbonImmutable::class)
            ->and($session->last_activity_at)->toBeInstanceOf(CarbonImmutable::class)
            ->and($session->consent_analytics)->toBeBool()->toBeTrue()
            ->and($session->pageview_count)->toBeInt()->toBe(4);
    });
});

describe('relationships', function () {
    it('belongs to a campaign', function () {
        $campaign = Campaign::factory()->create();
        $session = VisitSession::factory()->fromCampaign($campaign)->create();

        expect($session->campaign)->toBeInstanceOf(Campaign::class)
            ->and($session->campaign->id)->toBe($campaign->id);
    });

    it('has many page views', function () {
        $session = VisitSession::factory()->create();
        PageView::factory()->count(3)->create(['visit_session_id' => $session->id]);

        expect($session->pageViews)->toHaveCount(3);
    });
});

describe('scopes', function () {
    it('activeWindow keeps only sessions inside the 30 min window', function () {
        VisitSession::factory()->inWindow()->create();
        VisitSession::factory()->expired()->create();

        expect(VisitSession::query()->activeWindow()->count())->toBe(1);
    });

    it('bySource filters by source value', function () {
        VisitSession::factory()->direct()->create();
        VisitSession::factory()->fromSocial('instagram')->create();

        expect(VisitSession::query()->bySource('instagram')->count())->toBe(1);
    });

    it('withConsent keeps only consented sessions', function () {
        VisitSession::factory()->withConsent()->create();
        VisitSession::factory()->withoutConsent()->create();

        expect(VisitSession::query()->withConsent()->count())->toBe(1);
    });
});
