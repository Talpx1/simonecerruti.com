<?php

declare(strict_types=1);

use App\Actions\DetectVisitSource;
use App\Enums\VisitMediumType;
use App\Enums\VisitSourceType;
use App\Models\Campaign;

describe('utm_campaign matching', function () {
    it('binds to an active campaign by slug and inherits its source/medium', function () {
        $campaign = Campaign::factory()->create([
            'slug' => 'spring-launch',
            'source' => 'instagram',
            'medium' => VisitMediumType::SOCIAL->value,
        ]);

        $data = DetectVisitSource::run(
            utm_source: 'whatever',
            utm_medium: 'override',
            utm_campaign: 'spring-launch',
            utm_term: null,
            utm_content: null,
            referrer_url: null,
            current_host: 'example.test',
        );

        expect($data->source)->toBe('instagram')
            ->and($data->medium)->toBe(VisitMediumType::SOCIAL->value)
            ->and($data->campaign_id)->toBe($campaign->id)
            ->and($data->utm_campaign)->toBe('spring-launch');
    });

    it('ignores ended campaigns and falls back to raw utm_source', function () {
        Campaign::factory()->ended()->create([
            'slug' => 'dead-campaign',
            'source' => 'instagram',
        ]);

        $data = DetectVisitSource::run(
            utm_source: 'newsletter',
            utm_medium: 'email',
            utm_campaign: 'dead-campaign',
            utm_term: null,
            utm_content: null,
            referrer_url: null,
            current_host: 'example.test',
        );

        expect($data->source)->toBe('newsletter')
            ->and($data->medium)->toBe('email')
            ->and($data->campaign_id)->toBeNull();
    });

    it('falls back to UNKNOWN when no utm_source and no match', function () {
        $data = DetectVisitSource::run(
            utm_source: null,
            utm_medium: null,
            utm_campaign: 'unknown',
            utm_term: null,
            utm_content: null,
            referrer_url: null,
            current_host: 'example.test',
        );

        expect($data->source)->toBe(VisitSourceType::UNKNOWN->value)
            ->and($data->campaign_id)->toBeNull();
    });
});

describe('referrer matching', function () {
    it('marks same-host referrers as INTERNAL when no utm_source', function () {
        $data = DetectVisitSource::run(
            utm_source: null, utm_medium: null, utm_campaign: null, utm_term: null, utm_content: null,
            referrer_url: 'https://example.test/blog',
            current_host: 'example.test',
        );

        expect($data->source)->toBe(VisitSourceType::INTERNAL->value);
    });

    it('respects utm_source on same-host referrers', function () {
        $data = DetectVisitSource::run(
            utm_source: 'cross-promo', utm_medium: null, utm_campaign: null, utm_term: null, utm_content: null,
            referrer_url: 'https://example.test/blog',
            current_host: 'example.test',
        );

        expect($data->source)->toBe('cross-promo');
    });

    it('puts the social platform name in source and "social" in medium', function () {
        $data = DetectVisitSource::run(
            utm_source: null, utm_medium: null, utm_campaign: null, utm_term: null, utm_content: null,
            referrer_url: 'https://www.instagram.com/some-profile',
            current_host: 'example.test',
        );

        expect($data->source)->toBe('instagram')
            ->and($data->medium)->toBe(VisitMediumType::SOCIAL->value)
            ->and($data->referrer_host)->toBe('www.instagram.com');
    });

    it('puts the search engine name in source and "organic" in medium', function () {
        $data = DetectVisitSource::run(
            utm_source: null, utm_medium: null, utm_campaign: null, utm_term: null, utm_content: null,
            referrer_url: 'https://www.google.de/search?q=foo',
            current_host: 'example.test',
        );

        expect($data->source)->toBe('google')
            ->and($data->medium)->toBe(VisitMediumType::ORGANIC->value);
    });

    it('puts the referrer host in source and "referral" in medium for unknown referrers', function () {
        $data = DetectVisitSource::run(
            utm_source: null, utm_medium: null, utm_campaign: null, utm_term: null, utm_content: null,
            referrer_url: 'https://example.com/news/foo',
            current_host: 'example.test',
        );

        expect($data->source)->toBe('example.com')
            ->and($data->medium)->toBe(VisitMediumType::REFERRAL->value);
    });
});

describe('direct visits', function () {
    it('returns DIRECT when no utm and no referrer', function () {
        $data = DetectVisitSource::run(
            utm_source: null, utm_medium: null, utm_campaign: null, utm_term: null, utm_content: null,
            referrer_url: null,
            current_host: 'example.test',
        );

        expect($data->source)->toBe(VisitSourceType::DIRECT->value)
            ->and($data->medium)->toBeNull()
            ->and($data->referrer_host)->toBeNull();
    });
});
