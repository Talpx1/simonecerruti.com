<?php

declare(strict_types=1);

use App\Enums\VisitMediumType;
use App\Models\Campaign;
use App\Models\VisitSession;
use Carbon\CarbonImmutable;

describe('attributes', function (): void {
    describe('medium', function (): void {
        it('is cast to the VisitMediumType enum', function (): void {
            $campaign = Campaign::factory()->create(['medium' => VisitMediumType::SOCIAL]);

            expect($campaign->medium)->toBeInstanceOf(VisitMediumType::class)
                ->and($campaign->medium)->toBe(VisitMediumType::SOCIAL);
        });
    });

    describe('starts_at', function (): void {
        it('is cast to an immutable datetime', function (): void {
            $campaign = Campaign::factory()->scheduled()->create();

            expect($campaign->starts_at)->toBeInstanceOf(CarbonImmutable::class);
        });
    });

    describe('ends_at', function (): void {
        it('is cast to an immutable datetime', function (): void {
            $campaign = Campaign::factory()->scheduled()->create();

            expect($campaign->ends_at)->toBeInstanceOf(CarbonImmutable::class);
        });
    });
});

describe('scopes', function (): void {
    describe('active', function (): void {
        it('returns campaigns inside the starts_at and ends_at window', function (): void {
            Campaign::factory()->count(2)->create();
            Campaign::factory()->scheduled()->create();
            Campaign::factory()->ended()->create();

            expect(Campaign::query()->count())->toBe(4)
                ->and(Campaign::query()->active()->count())->toBe(2);
        });

        it('treats null starts_at as already started', function (): void {
            Campaign::factory()->create(['starts_at' => null, 'ends_at' => now()->addDay()]);

            expect(Campaign::query()->active()->count())->toBe(1);
        });

        it('treats null ends_at as indefinitely active', function (): void {
            Campaign::factory()->create(['starts_at' => now()->subDay(), 'ends_at' => null]);

            expect(Campaign::query()->active()->count())->toBe(1);
        });
    });
});

describe('relationships', function (): void {
    describe('visitSessions', function (): void {
        it('has many visit sessions', function (): void {
            $campaign = Campaign::factory()->create();

            expect($campaign->visitSessions()->count())->toBe(0)
                ->and(VisitSession::factory()->count(3)->fromCampaign($campaign)->create())->toHaveCount(3)
                ->and($campaign->refresh()->visitSessions)->toHaveCount(3);
        });
    });
});
