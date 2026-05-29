<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\VisitMediumType;
use App\Models\Campaign;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Campaign>
 */
class CampaignFactory extends Factory {
    /**
     * @return array<string, mixed>
     */
    public function definition(): array {
        $name = fake()->unique()->company();

        return [
            'name' => $name,
            'slug' => Str::slug($name.'-'.fake()->unique()->numerify('###')),
            'source' => fake()->randomElement(['instagram', 'facebook', 'newsletter', 'qr', 'linkedin']),
            'medium' => fake()->randomElement([VisitMediumType::SOCIAL->value, VisitMediumType::EMAIL->value, VisitMediumType::PRINT->value, null]),
            'description' => fake()->boolean(50) ? fake()->sentence() : null,
            'starts_at' => null,
            'ends_at' => null,
            'notes' => null,
        ];
    }

    public function scheduled(): static {
        return $this->state(fn (array $attributes): array => [
            'starts_at' => now()->addDays(7),
            'ends_at' => now()->addDays(30),
        ]);
    }

    public function ended(): static {
        return $this->state(fn (array $attributes): array => [
            'starts_at' => now()->subDays(30),
            'ends_at' => now()->subDay(),
        ]);
    }
}
