<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\SchemaType;
use App\Models\BlogArticle;
use App\Models\Seo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Seo>
 */
class SeoFactory extends Factory {
    /**
     * Define the model's default state.
     */
    public function definition(): array {
        return [
            'seoable_id' => BlogArticle::factory(),
            'seoable_type' => (new BlogArticle)->getMorphClass(),
            'title' => [
                'it' => fake('it')->sentence(),
                'en' => fake('en')->sentence(),
            ],
            'description' => [
                'it' => fake('it')->sentence(),
                'en' => fake('en')->sentence(),
            ],
            'schema_type' => SchemaType::ARTICLE,
        ];
    }
}
