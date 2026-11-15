<?php

declare(strict_types=1);

use App\Enums\TagTypes;
use App\Livewire\Pages\Project\ProjectList;
use App\Models\Project;
use App\Models\Tag;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    app()->setLocale('en');
});

describe('load more', function (): void {
    it('paginates published projects', function (): void {
        Project::factory()->published()->count(8)->create();
        Project::factory()->unpublished()->count(2)->create();

        $component = livewire(ProjectList::class);

        expect($component->instance()->getLoadMoreData('projects'))->toHaveCount(6)
            ->and($component->instance()->getLoadMoreTotal('projects'))->toBe(8)
            ->and($component->instance()->canLoadMore('projects'))->toBeTrue();

        $component->call('loadMore', 'projects');

        expect($component->instance()->getLoadMoreData('projects'))->toHaveCount(8)
            ->and($component->instance()->canLoadMore('projects'))->toBeFalse();
    });

    it('reports an empty state when there are no published projects', function (): void {
        $component = livewire(ProjectList::class);

        expect($component->instance()->isLoadMoreEmpty('projects'))->toBeTrue();
    });
});

describe('rendering', function (): void {
    it('renders featured projects as features and every project in the rail with its type tags', function (): void {
        $featured = Project::factory()->published()->featured()->create(['title' => ['en' => 'Featured Work', 'it' => 'Lavoro']]);
        $plain = Project::factory()->published()->create(['title' => ['en' => 'Plain Work', 'it' => 'Semplice']]);
        $tag = Tag::findOrCreate('CRM', TagTypes::TAG->value);
        $featured->attachTag($tag);
        $plain->attachTag($tag);

        livewire(ProjectList::class)
            ->assertSeeHtml('class="project-feature"')
            ->assertSee('Featured Work')
            ->assertSee('Plain Work')
            ->assertSeeHtml('data-types="CRM"')
            ->assertSeeHtml('data-f="CRM"');
    });

    it('shows the empty message when there are no projects', function (): void {
        livewire(ProjectList::class)
            ->assertSee('No projects yet.')
            ->assertDontSeeHtml('id="projects-rail"');
    });
});
