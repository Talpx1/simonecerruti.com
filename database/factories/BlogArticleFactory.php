<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\BlogArticleStatuses;
use App\Models\BlogArticle;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BlogArticle>
 */
class BlogArticleFactory extends Factory {
    /**
     * Define the model's default state.
     */
    public function definition(): array {
        return [
            'title' => [
                'it' => fake('it')->words(3, asText: true),
                'en' => fake('en')->words(3, asText: true),
            ],
            'slug' => [
                'it' => fake('it')->unique()->slug(),
                'en' => fake('en')->unique()->slug(),
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
            'author_id' => User::factory(),
            'status' => BlogArticleStatuses::PUBLISHED,
            'published_at' => now()->subDay(),
        ];
    }

    /**
     * A draft article (never publicly visible).
     */
    public function draft(): static {
        return $this->state(fn (array $attributes): array => [
            'status' => BlogArticleStatuses::DRAFT,
            'published_at' => null,
        ]);
    }

    /**
     * A published article with a past publish date.
     */
    public function published(): static {
        return $this->state(fn (array $attributes): array => [
            'status' => BlogArticleStatuses::PUBLISHED,
            'published_at' => now()->subDay(),
        ]);
    }

    /**
     * A published article whose publish date is in the future (not yet live).
     */
    public function scheduled(): static {
        return $this->state(fn (array $attributes): array => [
            'status' => BlogArticleStatuses::PUBLISHED,
            'published_at' => now()->addWeek(),
        ]);
    }

    /**
     * An archived article (crawlable but not in the published feed).
     */
    public function archived(): static {
        return $this->state(fn (array $attributes): array => [
            'status' => BlogArticleStatuses::ARCHIVED,
            'published_at' => now()->subMonth(),
        ]);
    }

    /**
     * A hidden article (not crawlable, not in the published feed).
     */
    public function hidden(): static {
        return $this->state(fn (array $attributes): array => [
            'status' => BlogArticleStatuses::HIDDEN,
            'published_at' => now()->subDay(),
        ]);
    }

    /**
     * A featured article.
     */
    public function featured(): static {
        return $this->state(fn (array $attributes): array => [
            'featured' => true,
        ]);
    }

    /**
     * An article without a publish date.
     */
    public function unpublished(): static {
        return $this->state(fn (array $attributes): array => [
            'published_at' => null,
        ]);
    }
}
