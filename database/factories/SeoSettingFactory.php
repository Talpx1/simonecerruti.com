<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\SchemaType;
use App\Models\SeoSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SeoSetting>
 */
class SeoSettingFactory extends Factory {
    /**
     * Define the model's default state.
     */
    public function definition(): array {
        return [
            'type' => SchemaType::PERSON,
            'name' => fake()->name(),
            'description' => [
                'it' => fake('it')->sentence(),
                'en' => fake('en')->sentence(),
            ],
            'social_profiles' => [fake()->url(), fake()->url()],
            'default_og_image' => null,
            'title_separator' => ' | ',
            'website_schema' => true,
        ];
    }
}
