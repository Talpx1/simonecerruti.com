<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\DeviceType;
use App\Enums\VisitMediumType;
use App\Enums\VisitSourceType;
use App\Models\Campaign;
use App\Models\VisitSession;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<VisitSession>
 */
class VisitSessionFactory extends Factory {
    /**
     * @return array<string, mixed>
     */
    public function definition(): array {
        $started_at = fake()->dateTimeBetween('-30 days', 'now');

        return [
            'visitor_id' => null,
            'source' => VisitSourceType::DIRECT->value,
            'medium' => null,
            'campaign_id' => null,
            'utm_source' => null,
            'utm_medium' => null,
            'utm_campaign' => null,
            'utm_term' => null,
            'utm_content' => null,
            'referrer_url' => null,
            'referrer_host' => null,
            'landing_path' => '/'.fake()->slug(),
            'landing_route_name' => null,
            'locale' => fake()->randomElement(['it', 'en']),
            'ip' => null,
            'user_agent' => null,
            'device_type' => null,
            'country' => fake()->countryCode(),
            'consent_analytics' => false,
            'started_at' => $started_at,
            'last_activity_at' => $started_at,
            'pageview_count' => 1,
        ];
    }

    public function direct(): static {
        return $this->state(fn (array $attributes): array => [
            'source' => VisitSourceType::DIRECT->value,
            'medium' => null,
            'referrer_url' => null,
            'referrer_host' => null,
        ]);
    }

    public function fromCampaign(Campaign $campaign): static {
        return $this->state(fn (array $attributes): array => [
            'source' => $campaign->source,
            'medium' => $campaign->medium?->value,
            'campaign_id' => $campaign->id,
            'utm_source' => $campaign->source,
            'utm_medium' => $campaign->medium?->value,
            'utm_campaign' => $campaign->slug,
        ]);
    }

    public function fromSocial(string $platform = 'instagram'): static {
        return $this->state(fn (array $attributes): array => [
            'source' => $platform,
            'medium' => VisitMediumType::SOCIAL->value,
            'referrer_url' => "https://{$platform}.com/",
            'referrer_host' => "{$platform}.com",
        ]);
    }

    public function fromSearch(string $engine = 'google'): static {
        return $this->state(fn (array $attributes): array => [
            'source' => $engine,
            'medium' => VisitMediumType::ORGANIC->value,
            'referrer_url' => "https://www.{$engine}.com/",
            'referrer_host' => "www.{$engine}.com",
        ]);
    }

    public function withConsent(): static {
        return $this->state(fn (array $attributes): array => [
            'consent_analytics' => true,
            'visitor_id' => (string) Str::uuid(),
            'ip' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'device_type' => fake()->randomElement([DeviceType::MOBILE->value, DeviceType::TABLET->value, DeviceType::DESKTOP->value]),
        ]);
    }

    public function withoutConsent(): static {
        return $this->state(fn (array $attributes): array => [
            'consent_analytics' => false,
            'visitor_id' => null,
            'ip' => null,
            'user_agent' => null,
            'device_type' => null,
        ]);
    }

    public function inWindow(): static {
        return $this->state(fn (array $attributes): array => [
            'last_activity_at' => now()->subMinutes(5),
            'started_at' => now()->subMinutes(10),
        ]);
    }

    public function expired(): static {
        return $this->state(fn (array $attributes): array => [
            'last_activity_at' => now()->subHours(2),
            'started_at' => now()->subHours(2),
        ]);
    }
}
