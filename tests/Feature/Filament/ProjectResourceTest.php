<?php

declare(strict_types=1);

use App\Enums\SchemaType;
use App\Enums\TagTypes;
use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Projects\Pages\EditProject;
use App\Filament\Resources\Projects\Pages\ListProjects;
use App\Models\Client;
use App\Models\Project;
use App\Models\Tag;
use Filament\Actions\Testing\TestAction;

use function Pest\Laravel\assertDatabaseHas;
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

it('creates a project for an existing client', function () {
    $client = Client::factory()->create();

    livewire(CreateProject::class)
        ->fillForm([
            'title' => 'Client Project',
            'slug' => 'client-project',
            'client_id' => $client->id,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Project::query()->firstOrFail()->client_id)->toBe($client->id);
});

it('creates a client from the project form select', function () {
    livewire(CreateProject::class)
        ->fillForm([
            'title' => 'Fresh Client Project',
            'slug' => 'fresh-client-project',
        ])
        ->callAction(TestAction::make('createOption')->schemaComponent('client_id'), [
            'name' => 'Brand New Client',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseHas(Client::class, ['name' => 'Brand New Client']);
    expect(Project::query()->firstOrFail()->client?->name)->toBe('Brand New Client');
});

it('translates the tags of a project from the tags input', function () {
    $project = Project::factory()->create();
    $project->syncTagsWithType(['Gestionale'], TagTypes::TAG->value);
    $tag = Tag::query()->where('type', TagTypes::TAG->value)->firstOrFail();

    livewire(EditProject::class, ['record' => $project->id])
        ->callAction(TestAction::make('translateTags')->schemaComponent('tags'), [
            'tags' => [['id' => $tag->id, 'names' => ['it' => 'Gestionale', 'en' => 'Management software']]],
        ])
        ->assertHasNoActionErrors();

    expect($tag->refresh()->getTranslationWithoutFallback('name', 'en'))->toBe('Management software');
});

it('saves the story fields of a project', function () {
    $project = Project::factory()->create();

    livewire(EditProject::class, ['record' => $project->id])
        ->fillForm([
            'starting_point' => 'Un gestionale obsoleto',
            'goal' => 'Unificare i processi',
            'outcome' => 'Meno tempo perso',
            'kinds' => ['Piattaforma web'],
            'features' => [
                ['title' => 'Dashboard', 'description' => 'Metriche in tempo reale'],
            ],
            'home_summary' => '<p>Un <strong>risultato</strong> concreto</p>',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $project->refresh();

    expect($project->getTranslation('starting_point', 'it'))->toBe('Un gestionale obsoleto')
        ->and($project->getTranslation('goal', 'it'))->toBe('Unificare i processi')
        ->and($project->getTranslation('outcome', 'it'))->toBe('Meno tempo perso')
        ->and($project->getTranslation('features', 'it'))->toHaveCount(1)
        ->and($project->getTranslation('home_summary', 'it'))->toContain('<strong>risultato</strong>')
        ->and($project->tagsWithType(TagTypes::PROJECT_KIND->value)->pluck('name')->all())->toBe(['Piattaforma web']);
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
