<?php

declare(strict_types=1);

namespace App\Actions;

use App\DataTransferObjects\VisitSourceData;
use App\Enums\VisitMediumType;
use App\Enums\VisitSourceType;
use App\Models\Campaign;
use Lorisleiva\Actions\Concerns\AsAction;

class DetectVisitSource {
    use AsAction;

    public function handle(
        ?string $utm_source,
        ?string $utm_medium,
        ?string $utm_campaign,
        ?string $utm_term,
        ?string $utm_content,
        ?string $referrer_url,
        string $current_host,
    ): VisitSourceData {
        if ($utm_campaign !== null && $utm_campaign !== '') {
            $campaign = Campaign::query()->active()->where('slug', $utm_campaign)->first();

            return new VisitSourceData(
                source: $campaign->source ?? $utm_source ?? VisitSourceType::UNKNOWN->value,
                medium: $campaign?->medium?->value ?? $utm_medium,
                campaign_id: $campaign?->id,
                utm_source: $utm_source,
                utm_medium: $utm_medium,
                utm_campaign: $utm_campaign,
                utm_term: $utm_term,
                utm_content: $utm_content,
                referrer_url: $referrer_url,
                referrer_host: $this->parseHost($referrer_url),
            );
        }

        if ($referrer_url !== null && $referrer_url !== '') {
            $referrer_host = $this->parseHost($referrer_url);

            if ($referrer_host !== null && strcasecmp($referrer_host, $current_host) === 0) {
                return new VisitSourceData(
                    source: $utm_source ?? VisitSourceType::INTERNAL->value,
                    medium: $utm_medium,
                    utm_source: $utm_source,
                    utm_medium: $utm_medium,
                    utm_campaign: $utm_campaign,
                    utm_term: $utm_term,
                    utm_content: $utm_content,
                    referrer_url: $referrer_url,
                    referrer_host: $referrer_host,
                );
            }

            $social_source = $this->matchSocial($referrer_host);
            if ($social_source !== null) {
                return new VisitSourceData(
                    source: $social_source,
                    medium: VisitMediumType::SOCIAL->value,
                    utm_source: $utm_source,
                    utm_medium: $utm_medium,
                    utm_campaign: $utm_campaign,
                    utm_term: $utm_term,
                    utm_content: $utm_content,
                    referrer_url: $referrer_url,
                    referrer_host: $referrer_host,
                );
            }

            $search_source = $this->matchSearch($referrer_host);
            if ($search_source !== null) {
                return new VisitSourceData(
                    source: $search_source,
                    medium: VisitMediumType::ORGANIC->value,
                    utm_source: $utm_source,
                    utm_medium: $utm_medium,
                    utm_campaign: $utm_campaign,
                    utm_term: $utm_term,
                    utm_content: $utm_content,
                    referrer_url: $referrer_url,
                    referrer_host: $referrer_host,
                );
            }

            return new VisitSourceData(
                source: $referrer_host ?? VisitSourceType::UNKNOWN->value,
                medium: VisitMediumType::REFERRAL->value,
                utm_source: $utm_source,
                utm_medium: $utm_medium,
                utm_campaign: $utm_campaign,
                utm_term: $utm_term,
                utm_content: $utm_content,
                referrer_url: $referrer_url,
                referrer_host: $referrer_host,
            );
        }

        return new VisitSourceData(
            source: VisitSourceType::DIRECT->value,
            utm_source: $utm_source,
            utm_medium: $utm_medium,
            utm_campaign: $utm_campaign,
            utm_term: $utm_term,
            utm_content: $utm_content,
        );
    }

    private function parseHost(?string $url): ?string {
        if ($url === null || $url === '') {
            return null;
        }

        $host = parse_url($url, PHP_URL_HOST);

        return is_string($host) ? strtolower($host) : null;
    }

    private function matchSocial(?string $host): ?string {
        if ($host === null) {
            return null;
        }

        /** @var array<string, string> $social_hosts */
        $social_hosts = config()->array('analytics.social_hosts');

        return $social_hosts[$host] ?? null;
    }

    private function matchSearch(?string $host): ?string {
        if ($host === null) {
            return null;
        }

        /** @var array<string, string> $search_hosts */
        $search_hosts = config()->array('analytics.search_hosts');

        foreach ($search_hosts as $needle => $source_name) {
            if (str_contains($host, $needle)) {
                return $source_name;
            }
        }

        return null;
    }
}
