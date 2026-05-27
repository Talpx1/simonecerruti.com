<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\BlogArticle;
use App\Models\BlogArticleRelatable;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BlogArticleRelatable>
 */
class BlogArticleRelatableFactory extends Factory {
    /**
     * Define the model's default state.
     */
    public function definition(): array {
        return [
            'blog_article_id' => BlogArticle::factory(),
            'relatable_type' => Project::class,
            'relatable_id' => Project::factory(),
        ];
    }

    /**
     * Relate the article to a project.
     */
    public function forProject(?Project $project = null): static {
        return $this->state(fn (array $attributes): array => [
            'relatable_type' => Project::class,
            'relatable_id' => $project?->getKey() ?? Project::factory(),
        ]);
    }

    /**
     * Relate the article to another blog article.
     */
    public function forBlogArticle(?BlogArticle $blog_article = null): static {
        return $this->state(fn (array $attributes): array => [
            'relatable_type' => BlogArticle::class,
            'relatable_id' => $blog_article?->getKey() ?? BlogArticle::factory(),
        ]);
    }
}
