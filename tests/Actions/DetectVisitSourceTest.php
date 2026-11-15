<?php

declare(strict_types=1);

use App\Enums\VisitMediumType;
use App\Enums\VisitSourceType;
use App\Models\Campaign;

describe('utm_campaign matching', function (): void {
    it('binds to an active campaign by slug and inherits its source and medium', function (): void {
        $campaign = Campaign::factory()->create([
            'slug' => 'spring-launch',
            'source' => 'instagram',
            'medium' => VisitMediumType::SOCIAL,
        ]);

        $data = detectVisitSource(utm_source: 'whatever', utm_medium: 'override', utm_campaign: 'spring-launch');

        expect($data->source)->toBe('instagram')
            ->and($data->medium)->toBe(VisitMediumType::SOCIAL->value)
            ->and($data->campaign_id)->toBe($campaign->id)
            ->and($data->utm_campaign)->toBe('spring-launch');
    });

    it('falls back to utm_medium when the campaign has no medium', function (): void {
        Campaign::factory()->create([
            'slug' => 'no-medium',
            'source' => 'newsletter',
            'medium' => null,
        ]);

        $data = detectVisitSource(utm_medium: VisitMediumType::EMAIL->value, utm_campaign: 'no-medium');

        expect($data->source)->toBe('newsletter')
            ->and($data->medium)->toBe(VisitMediumType::EMAIL->value);
    });

    it('ignores ended campaigns and falls back to the raw utm_source', function (): void {
        Campaign::factory()->ended()->create([
            'slug' => 'dead-campaign',
            'source' => 'instagram',
        ]);

        $data = detectVisitSource(utm_source: 'newsletter', utm_medium: VisitMediumType::EMAIL->value, utm_campaign: 'dead-campaign');

        expect($data->source)->toBe('newsletter')
            ->and($data->medium)->toBe(VisitMediumType::EMAIL->value)
            ->and($data->campaign_id)->toBeNull();
    });

    it('falls back to unknown when there is no utm_source and no campaign matches', function (): void {
        $data = detectVisitSource(utm_campaign: 'unknown');

        expect($data->source)->toBe(VisitSourceType::UNKNOWN->value)
            ->and($data->campaign_id)->toBeNull();
    });
});

describe('referrer matching', function (): void {
    it('marks same-host referrers as internal when there is no utm_source', function (): void {
        expect(detectVisitSource(referrer_url: 'https://example.test/blog')->source)->toBe(VisitSourceType::INTERNAL->value);
    });

    it('respects utm_source on same-host referrers', function (): void {
        expect(detectVisitSource(utm_source: 'cross-promo', referrer_url: 'https://example.test/blog')->source)->toBe('cross-promo');
    });

    it('puts the social platform name in source and social in medium', function (): void {
        $data = detectVisitSource(referrer_url: 'https://www.instagram.com/some-profile');

        expect($data->source)->toBe('instagram')
            ->and($data->medium)->toBe(VisitMediumType::SOCIAL->value)
            ->and($data->referrer_host)->toBe('www.instagram.com');
    });

    it('puts the search engine name in source and organic in medium', function (): void {
        $data = detectVisitSource(referrer_url: 'https://www.google.de/search?q=foo');

        expect($data->source)->toBe('google')
            ->and($data->medium)->toBe(VisitMediumType::ORGANIC->value);
    });

    it('puts the referrer host in source and referral in medium for unknown referrers', function (): void {
        $data = detectVisitSource(referrer_url: 'https://example.com/news/foo');

        expect($data->source)->toBe('example.com')
            ->and($data->medium)->toBe(VisitMediumType::REFERRAL->value);
    });
});

describe('direct visits', function (): void {
    it('returns direct when there is no utm and no referrer', function (): void {
        $data = detectVisitSource();

        expect($data->source)->toBe(VisitSourceType::DIRECT->value)
            ->and($data->medium)->toBeNull()
            ->and($data->referrer_host)->toBeNull();
    });
});

describe('malformed referrers', function (): void {
    it('treats an unparsable referrer url as no referrer', function (): void {
        $data = detectVisitSource(referrer_url: 'http:///broken');

        expect($data->source)->toBe(VisitSourceType::UNKNOWN->value)
            ->and($data->referrer_host)->toBeNull();
    });
});
