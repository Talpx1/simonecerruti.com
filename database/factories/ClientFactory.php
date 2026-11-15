<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory {
    /**
     * Define the model's default state.
     *
     * @return array<model-property<Client>, mixed>
     */
    public function definition(): array {
        return [
            'name' => fake()->unique()->company(),
            'description' => [
                'it' => fake('it')->sentence(),
                'en' => fake('en')->sentence(),
            ],
        ];
    }

    /**
     * A client without a description.
     */
    public function withoutDescription(): static {
        return $this->state(fn (array $attributes): array => [
            'description' => null,
        ]);
    }
}
