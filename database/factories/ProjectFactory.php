<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory {
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array {
        return [
            'title' => [
                'it' => fake('it')->words(3, asText: true),
                'en' => fake('en')->words(3, asText: true),
            ],
            'client' => fake()->company(),
            'slug' => [
                'it' => fake('it')->slug(),
                'en' => fake('en')->slug(),
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
            'published' => fake()->boolean(70),
            'featured' => false,
        ];
    }
}
