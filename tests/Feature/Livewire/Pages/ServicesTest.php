<?php

declare(strict_types=1);

use App\Livewire\Pages\Services;
use App\Models\Project;

use function Pest\Livewire\livewire;

beforeEach(fn () => $this->withoutVite());

it('renders with no featured content', function () {
    livewire(Services::class)
        ->assertOk()
        ->assertDontSeeHtml('cta-services-area-1');
});

it('surfaces the featured project in the Area 01 contextual CTA', function () {
    $project = Project::factory()->published()->featured()->create();

    livewire(Services::class)
        ->assertOk()
        ->assertSee($project->title)
        ->assertSee(__('Project:'))
        ->assertSeeHtml(route('project.show', $project->slug))
        ->assertSeeHtml('data-pan="cta-services-area-1"');
});

it('keeps the Area 02 and Area 03 contextual CTAs hidden', function () {
    Project::factory()->published()->featured()->create();

    livewire(Services::class)
        ->assertOk()
        ->assertDontSeeHtml('cta-services-area-2')
        ->assertDontSeeHtml('cta-services-area-3');
});
