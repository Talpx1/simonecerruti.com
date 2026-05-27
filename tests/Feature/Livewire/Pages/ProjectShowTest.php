<?php

declare(strict_types=1);

use App\Livewire\Pages\Project\ProjectShow;
use App\Models\Project;

use function Pest\Livewire\livewire;

beforeEach(fn () => $this->withoutVite());

it('renders a project', function () {
    $project = Project::factory()->published()->create();

    $component = livewire(ProjectShow::class, ['project' => $project])
        ->assertOk();

    expect($component->instance()->project->id)->toBe($project->id);
});
