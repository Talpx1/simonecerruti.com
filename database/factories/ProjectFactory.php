<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory {
    /**
     * Define the model's default state.
     */
    public function definition(): array {
        return [
            'title' => [
                'it' => fake('it')->words(3, asText: true),
                'en' => fake('en')->words(3, asText: true),
            ],
            'client' => fake()->company(),
            'slug' => [
                'it' => fake('it')->unique()->slug(),
                'en' => fake('en')->unique()->slug(),
            ],
            'short_description' => [
                'it' => fake('it')->paragraph(),
                'en' => fake('en')->paragraph(),
            ],
            'description' => [
                'it' => fake('it')->paragraphs(asText: true),
                'en' => fake('en')->paragraphs(asText: true),
            ],
            'external_link' => fake()->boolean(60) ? [
                'it' => fake()->url(),
                'en' => fake()->url(),
            ] : null,
            'links' => [
                ['url' => 'https://github.com/'.fake()->userName()],
                ['url' => fake()->url()],
            ],
            'published' => true,
            'featured' => false,
        ];
    }

    /**
     * A published project.
     */
    public function published(): static {
        return $this->state(fn (array $attributes): array => [
            'published' => true,
        ]);
    }

    /**
     * An unpublished project.
     */
    public function unpublished(): static {
        return $this->state(fn (array $attributes): array => [
            'published' => false,
        ]);
    }

    /**
     * A featured project.
     */
    public function featured(): static {
        return $this->state(fn (array $attributes): array => [
            'featured' => true,
        ]);
    }
}
