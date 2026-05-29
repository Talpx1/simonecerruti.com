<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\PageView;
use App\Models\VisitSession;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PageView>
 */
class PageViewFactory extends Factory {
    /**
     * @return array<string, mixed>
     */
    public function definition(): array {
        return [
            'visit_session_id' => VisitSession::factory(),
            'url_path' => '/'.fake()->slug(),
            'route_name' => null,
            'locale' => fake()->randomElement(['it', 'en']),
            'viewed_at' => now(),
        ];
    }
}
