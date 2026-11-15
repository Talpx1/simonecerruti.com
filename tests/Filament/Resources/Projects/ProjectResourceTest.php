<?php

declare(strict_types=1);

use App\Enums\SchemaType;
use App\Enums\TagTypes;
use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Projects\Pages\EditProject;
use App\Filament\Resources\Projects\Pages\ListProjects;
use App\Filament\Resources\Projects\ProjectResource;
use App\Models\Client;
use App\Models\Project;
use App\Models\Tag;
use Filament\Actions\Testing\TestAction;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;

beforeEach(function (): void {
    authenticateAsAdmin();
});

describe('access', function (): void {
    it('forbids users who cannot access the panel', function (): void {
        $project = Project::factory()->create();

        assertPanelUrlIsForbiddenForNonAdminUser(ProjectResource::getUrl('index'));
        assertPanelUrlIsForbiddenForNonAdminUser(ProjectResource::getUrl('create'));
        assertPanelUrlIsForbiddenForNonAdminUser(ProjectResource::getUrl('edit', ['record' => $project]));
    });

    it('redirects guests to the login page', function (): void {
        $project = Project::factory()->create();

        assertPanelUrlRedirectsGuestToLogin(ProjectResource::getUrl('index'));
        assertPanelUrlRedirectsGuestToLogin(ProjectResource::getUrl('create'));
        assertPanelUrlRedirectsGuestToLogin(ProjectResource::getUrl('edit', ['record' => $project]));
    });
});

describe('list page', function (): void {
    it('lists projects', function (): void {
        $projects = Project::factory()->count(3)->create();

        livewire(ListProjects::class)
            ->assertCanSeeTableRecords($projects);
    });
});

describe('create page', function (): void {
    it('creates a project', function (): void {
        expect(Project::query()->count())->toBe(0);

        livewire(CreateProject::class)
            ->fillForm([
                'title' => 'A Brand New Project',
                'slug' => 'a-brand-new-project',
            ])
            ->call('create')
            ->assertNotified()
            ->assertHasNoFormErrors();

        expect(Project::query()->count())->toBe(1);
    });

    it('validates the required fields', function (): void {
        livewire(CreateProject::class)
            ->fillForm([
                'title' => null,
                'slug' => null,
            ])
            ->call('create')
            ->assertHasFormErrors([
                'title' => 'required',
                'slug' => 'required',
            ])
            ->assertNotNotified();

        expect(Project::query()->count())->toBe(0);
    });

    it('creates a project for an existing client', function (): void {
        $client = Client::factory()->create();

        expect(Project::query()->where('client_id', $client->id)->exists())->toBeFalse();

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

    it('creates a client from the project form select', function (): void {
        expect(Client::query()->where('name', 'Brand New Client')->exists())->toBeFalse();

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

    describe('slug uniqueness', function (): void {
        it('rejects a duplicate slug even when the title differs', function (): void {
            Project::factory()->withTitleAndSlug('Original Title', 'taken-slug')->create();

            livewire(CreateProject::class)
                ->fillForm([
                    'title' => 'A Completely Different Title',
                    'slug' => 'taken-slug',
                ])
                ->call('create')
                ->assertHasFormErrors(['slug'])
                ->assertNotNotified();

            expect(Project::query()->count())->toBe(1);
        });

        it('allows a duplicate title when the slug is unique', function (): void {
            Project::factory()->withTitleAndSlug('Shared Title', 'slug-one')->create();

            livewire(CreateProject::class)
                ->fillForm([
                    'title' => 'Shared Title',
                    'slug' => 'slug-two',
                ])
                ->call('create')
                ->assertNotified()
                ->assertHasNoFormErrors();

            expect(Project::query()->count())->toBe(2);
        });
    });
});

describe('edit page', function (): void {
    it('saves the story fields of a project', function (): void {
        $project = Project::factory()->create();

        expect($project->getTranslations('starting_point'))->toBeEmpty()
            ->and($project->tagsWithType(TagTypes::PROJECT_KIND->value))->toBeEmpty();

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
            ->assertNotified()
            ->assertHasNoFormErrors();

        $project->refresh();

        expect($project->getTranslation('starting_point', 'it'))->toBe('Un gestionale obsoleto')
            ->and($project->getTranslation('goal', 'it'))->toBe('Unificare i processi')
            ->and($project->getTranslation('outcome', 'it'))->toBe('Meno tempo perso')
            ->and($project->getTranslation('features', 'it'))->toHaveCount(1)
            ->and($project->getTranslation('home_summary', 'it'))->toContain('<strong>risultato</strong>')
            ->and($project->tagsWithType(TagTypes::PROJECT_KIND->value)->pluck('name')->all())->toBe(['Piattaforma web']);
    });

    it('translates the tags of a project from the tags input', function (): void {
        $project = Project::factory()->create();
        $project->syncTagsWithType(['Gestionale'], TagTypes::TAG->value);
        $tag = Tag::query()->where('type', TagTypes::TAG->value)->firstOrFail();

        expect($tag->getTranslationWithoutFallback('name', 'en'))->toBe('Gestionale');

        livewire(EditProject::class, ['record' => $project->id])
            ->callAction(TestAction::make('translateTags')->schemaComponent('tags'), [
                'tags' => [['id' => $tag->id, 'names' => ['it' => 'Gestionale', 'en' => 'Management software']]],
            ])
            ->assertHasNoActionErrors();

        expect($tag->refresh()->getTranslationWithoutFallback('name', 'en'))->toBe('Management software');
    });

    describe('SEO overrides', function (): void {
        it('saves SEO overrides per locale', function (): void {
            $project = Project::factory()->create();

            expect($project->seo)->toBeNull();

            $component = livewire(EditProject::class, ['record' => $project->id]);

            $component->fillForm([
                'seo.title' => 'Titolo Progetto',
                'seo.schema_type' => SchemaType::PRODUCT->value,
            ])->call('save')->assertHasNoFormErrors();

            $component->call('setActiveLocale', 'en')
                ->fillForm(['seo.title' => 'Project Title'])
                ->call('save')
                ->assertHasNoFormErrors();

            $project->refresh()->load('seo');

            expect($project->seo->getTranslation('title', 'it', false))->toBe('Titolo Progetto')
                ->and($project->seo->getTranslation('title', 'en', false))->toBe('Project Title')
                ->and($project->seo->schema_type)->toBe(SchemaType::PRODUCT);
        });
    });
});
