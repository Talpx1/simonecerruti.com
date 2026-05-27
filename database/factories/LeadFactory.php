<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ContactLead;
use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lead>
 */
class LeadFactory extends Factory {
    /**
     * Define the model's default state.
     */
    public function definition(): array {
        return [
            'leadable_type' => ContactLead::class,
            'leadable_id' => ContactLead::factory(),
            'read_at' => null,
            'is_pinned' => false,
        ];
    }

    /**
     * Indicate that the lead has been read.
     */
    public function read(): static {
        return $this->state(fn (array $attributes): array => [
            'read_at' => now(),
        ]);
    }

    /**
     * Indicate that the lead is pinned.
     */
    public function pinned(): static {
        return $this->state(fn (array $attributes): array => [
            'is_pinned' => true,
        ]);
    }

    /**
     * Attach the lead to a specific contact lead.
     */
    public function forContactLead(?ContactLead $contact_lead = null): static {
        return $this->state(fn (array $attributes): array => [
            'leadable_type' => ContactLead::class,
            'leadable_id' => $contact_lead?->getKey() ?? ContactLead::factory(),
        ]);
    }
}
