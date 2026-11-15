<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\TagTypes;
use App\Models\Client;
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
            'client_id' => fake()->boolean(80) ? Client::factory() : null,
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
     * A project with every optional story field filled in.
     */
    public function withStory(): static {
        return $this->state(fn (array $attributes): array => [
            'starting_point' => ['it' => fake('it')->paragraph(), 'en' => fake('en')->paragraph()],
            'goal' => ['it' => fake('it')->paragraph(), 'en' => fake('en')->paragraph()],
            'outcome' => ['it' => fake('it')->sentence(), 'en' => fake('en')->sentence()],
            'features' => [
                'it' => [['title' => fake('it')->words(2, asText: true), 'description' => fake('it')->sentence()]],
                'en' => [['title' => fake('en')->words(2, asText: true), 'description' => fake('en')->sentence()]],
            ],
            'home_summary' => ['it' => fake('it')->sentence(), 'en' => fake('en')->sentence()],
        ]);
    }

    /**
     * A project tagged with the given kinds.
     *
     * @param  list<string>  $kind_names
     */
    public function withKind(array $kind_names = ['Custom CRM']): static {
        return $this->afterCreating(function (Project $project) use ($kind_names): void {
            $project->attachTags($kind_names, TagTypes::PROJECT_KIND->value);
        });
    }

    /**
     * A project without a client.
     */
    public function withoutClient(): static {
        return $this->state(fn (array $attributes): array => [
            'client_id' => null,
        ]);
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

    /**
     * A project with the same title and slug in every locale.
     */
    public function withTitleAndSlug(string $title, string $slug): static {
        return $this->state(fn (array $attributes): array => [
            'title' => ['en' => $title, 'it' => $title],
            'slug' => ['en' => $slug, 'it' => $slug],
        ]);
    }
}
