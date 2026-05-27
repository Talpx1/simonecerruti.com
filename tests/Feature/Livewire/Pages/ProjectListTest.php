<?php

declare(strict_types=1);

use App\Livewire\Pages\Project\ProjectList;
use App\Models\Project;

use function Pest\Livewire\livewire;

beforeEach(fn () => $this->withoutVite());

it('paginates published projects with load-more', function () {
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

it('reports an empty state when there are no published projects', function () {
    $component = livewire(ProjectList::class);

    expect($component->instance()->isLoadMoreEmpty('projects'))->toBeTrue();
});
