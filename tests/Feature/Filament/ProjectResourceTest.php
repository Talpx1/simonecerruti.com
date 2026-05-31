<?php

declare(strict_types=1);

use App\Enums\SchemaType;
use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Projects\Pages\EditProject;
use App\Filament\Resources\Projects\Pages\ListProjects;
use App\Models\Project;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->withoutVite();
    actingAsAdmin();
});

it('lists projects', function () {
    $projects = Project::factory()->count(3)->create();

    livewire(ListProjects::class)
        ->assertCanSeeTableRecords($projects);
});

it('creates a project', function () {
    livewire(CreateProject::class)
        ->fillForm([
            'title' => 'A Brand New Project',
            'slug' => 'a-brand-new-project',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Project::query()->count())->toBe(1);
});

it('saves SEO overrides for a project per locale', function () {
    $project = Project::factory()->create();

    $component = livewire(EditProject::class, ['record' => $project->id]);

    $component->fillForm([
        'seo.title' => 'Titolo Progetto',
        'seo.schema_type' => SchemaType::PRODUCT->value,
    ])->call('save')->assertHasNoFormErrors();

    $component->call('setActiveLocale', 'en')
        ->fillForm(['seo.title' => 'Project Title'])
        ->call('save')->assertHasNoFormErrors();

    $project->refresh()->load('seo');

    expect($project->seo->getTranslation('title', 'it', false))->toBe('Titolo Progetto')
        ->and($project->seo->getTranslation('title', 'en', false))->toBe('Project Title')
        ->and($project->seo->schema_type)->toBe(SchemaType::PRODUCT);
});

describe('slug uniqueness (regression: uniqueness must check the slug column, not title)', function () {
    it('rejects a duplicate slug even when the title differs', function () {
        Project::factory()->create([
            'title' => ['en' => 'Original Title', 'it' => 'Original Title'],
            'slug' => ['en' => 'taken-slug', 'it' => 'taken-slug'],
        ]);

        livewire(CreateProject::class)
            ->fillForm([
                'title' => 'A Completely Different Title',
                'slug' => 'taken-slug',
            ])
            ->call('create')
            ->assertHasFormErrors(['slug']);
    });

    it('allows a duplicate title when the slug is unique', function () {
        Project::factory()->create([
            'title' => ['en' => 'Shared Title', 'it' => 'Shared Title'],
            'slug' => ['en' => 'slug-one', 'it' => 'slug-one'],
        ]);

        livewire(CreateProject::class)
            ->fillForm([
                'title' => 'Shared Title',
                'slug' => 'slug-two',
            ])
            ->call('create')
            ->assertHasNoFormErrors();
    });
});
