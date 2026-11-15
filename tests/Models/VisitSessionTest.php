<?php

declare(strict_types=1);

use App\Models\Campaign;
use App\Models\PageView;
use App\Models\VisitSession;
use Carbon\CarbonImmutable;

describe('attributes', function (): void {
    describe('id', function (): void {
        it('is a uuid string', function (): void {
            $session = VisitSession::factory()->create();

            expect($session->id)->toBeString()->toMatch('/^[0-9a-f-]{36}$/i');
        });
    });

    describe('started_at', function (): void {
        it('is cast to an immutable datetime', function (): void {
            expect(VisitSession::factory()->create()->started_at)->toBeInstanceOf(CarbonImmutable::class);
        });
    });

    describe('last_activity_at', function (): void {
        it('is cast to an immutable datetime', function (): void {
            expect(VisitSession::factory()->create()->last_activity_at)->toBeInstanceOf(CarbonImmutable::class);
        });
    });

    describe('consent_analytics', function (): void {
        it('is cast to a boolean', function (): void {
            expect(VisitSession::factory()->withConsent()->create()->consent_analytics)->toBeBool()->toBeTrue()
                ->and(VisitSession::factory()->withoutConsent()->create()->consent_analytics)->toBeBool()->toBeFalse();
        });
    });

    describe('pageview_count', function (): void {
        it('is cast to an integer', function (): void {
            $session = VisitSession::factory()->create(['pageview_count' => '4']);

            expect($session->pageview_count)->toBeInt()->toBe(4);
        });
    });
});

describe('scopes', function (): void {
    describe('activeWindow', function (): void {
        it('keeps only sessions inside the 30 min window', function (): void {
            VisitSession::factory()->inWindow()->create();
            VisitSession::factory()->expired()->create();

            expect(VisitSession::query()->count())->toBe(2)
                ->and(VisitSession::query()->activeWindow()->count())->toBe(1);
        });
    });

    describe('bySource', function (): void {
        it('filters by source value', function (): void {
            VisitSession::factory()->direct()->create();
            VisitSession::factory()->fromSocial('instagram')->create();

            expect(VisitSession::query()->count())->toBe(2)
                ->and(VisitSession::query()->bySource('instagram')->count())->toBe(1)
                ->and(VisitSession::query()->bySource('facebook')->count())->toBe(0);
        });
    });

    describe('withConsent', function (): void {
        it('keeps only consented sessions', function (): void {
            VisitSession::factory()->withConsent()->create();
            VisitSession::factory()->withoutConsent()->create();

            expect(VisitSession::query()->count())->toBe(2)
                ->and(VisitSession::query()->withConsent()->count())->toBe(1);
        });
    });
});

describe('relationships', function (): void {
    describe('campaign', function (): void {
        it('belongs to a campaign', function (): void {
            $campaign = Campaign::factory()->create();
            $session = VisitSession::factory()->fromCampaign($campaign)->create();

            expect($session->campaign)->toBeInstanceOf(Campaign::class)
                ->and($session->campaign->id)->toBe($campaign->id);
        });
    });

    describe('pageViews', function (): void {
        it('has many page views', function (): void {
            $session = VisitSession::factory()->create();

            expect($session->pageViews()->count())->toBe(0)
                ->and(PageView::factory()->count(3)->for($session)->create())->toHaveCount(3)
                ->and($session->refresh()->pageViews)->toHaveCount(3);
        });
    });
});
