<?php

declare(strict_types=1);

use App\Enums\VisitMediumType;
use App\Models\Campaign;
use App\Models\VisitSession;
use Carbon\CarbonImmutable;

describe('casts', function () {
    it('casts medium to VisitMediumType enum', function () {
        $campaign = Campaign::factory()->create(['medium' => VisitMediumType::SOCIAL->value]);

        expect($campaign->medium)->toBeInstanceOf(VisitMediumType::class)
            ->and($campaign->medium)->toBe(VisitMediumType::SOCIAL);
    });

    it('casts starts_at and ends_at to immutable datetimes', function () {
        $campaign = Campaign::factory()->scheduled()->create();

        expect($campaign->starts_at)->toBeInstanceOf(CarbonImmutable::class)
            ->and($campaign->ends_at)->toBeInstanceOf(CarbonImmutable::class);
    });
});

describe('active scope', function () {
    it('returns campaigns inside the starts_at / ends_at window', function () {
        Campaign::factory()->count(2)->create();
        Campaign::factory()->scheduled()->create();
        Campaign::factory()->ended()->create();

        expect(Campaign::query()->active()->count())->toBe(2);
    });

    it('treats null starts_at as already started', function () {
        Campaign::factory()->create(['starts_at' => null, 'ends_at' => now()->addDay()]);

        expect(Campaign::query()->active()->count())->toBe(1);
    });

    it('treats null ends_at as indefinitely active', function () {
        Campaign::factory()->create(['starts_at' => now()->subDay(), 'ends_at' => null]);

        expect(Campaign::query()->active()->count())->toBe(1);
    });
});

describe('visitSessions relation', function () {
    it('has many visit sessions', function () {
        $campaign = Campaign::factory()->create();
        VisitSession::factory()->count(3)->fromCampaign($campaign)->create();

        expect($campaign->visitSessions)->toHaveCount(3);
    });
});
