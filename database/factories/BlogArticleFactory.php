<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\BlogArticleStatuses;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BlogArticle>
 */
class BlogArticleFactory extends Factory {
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
            'slug' => [
                'it' => fake('it')->slug(),
                'en' => fake('en')->slug(),
            ],
            'summary' => [
                'it' => fake('it')->paragraph(),
                'en' => fake('en')->paragraph(),
            ],
            'content' => [
                'it' => fake('it')->paragraphs(asText: true),
                'en' => fake('en')->paragraphs(asText: true),
            ],
            'featured' => false,
            'featured_image_path' => fake()->filePath(),
            'author_id' => User::factory(),
            'status' => BlogArticleStatuses::PUBLISHED,
            'published_at' => now(),
        ];
    }
}
