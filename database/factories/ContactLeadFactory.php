<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ContactLead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContactLead>
 */
class ContactLeadFactory extends Factory {
    /**
     * Define the model's default state.
     */
    public function definition(): array {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'company_name' => fake()->boolean(40) ? fake()->company() : null,
            'email' => fake()->safeEmail(),
            'phone' => fake()->boolean(70) ? fake()->phoneNumber() : null,
            'message' => fake()->paragraph(),
            'acceptance' => true,
            'ip' => fake()->ipv4(),
        ];
    }

    /**
     * Indicate that the contact lead belongs to a company.
     */
    public function withCompany(): static {
        return $this->state(fn (array $attributes): array => [
            'company_name' => fake()->company(),
        ]);
    }

    /**
     * Indicate that the contact lead has no company.
     */
    public function withoutCompany(): static {
        return $this->state(fn (array $attributes): array => [
            'company_name' => null,
        ]);
    }
}
