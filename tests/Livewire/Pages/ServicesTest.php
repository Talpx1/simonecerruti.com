<?php

declare(strict_types=1);

use App\Enums\ServiceAreas;
use App\Livewire\Pages\Services;
use App\Models\Project;

use function Pest\Livewire\livewire;

it('renders the hero, the goals and the three service cards', function (): void {
    livewire(Services::class)
        ->assertOk()
        ->assertSeeHtml('data-pan="section-impression-services-hero"')
        ->assertSeeHtml('data-pan="cta-services-contacts"')
        ->assertSeeHtml('data-effect="services-routes"')
        ->assertSeeHtml('data-effect="services-hub"')
        ->assertSeeHtml('id="area-management"')
        ->assertSeeHtml('id="area-web"')
        ->assertSeeHtml('id="area-consulting"')
        ->assertSee(__('Manage the company better'))
        ->assertSee(__('Sell online'));
});

it('links every card to its dedicated landing page', function (ServiceAreas $area): void {
    livewire(Services::class)
        ->assertOk()
        ->assertSeeHtml('href="'.route($area->getRouteName()).'"');
})->with(ServiceAreas::cases());

describe('example project', function (): void {
    it('is not shown when nothing is tagged with a service', function (): void {
        Project::factory()->published()->featured()->create();

        livewire(Services::class)
            ->assertOk()
            ->assertDontSeeHtml('class="services-example"');
    });

    it('is shown for each area from its service tag', function (): void {
        $project = Project::factory()->published()->create();

        livewire(Services::class)
            ->assertDontSee($project->title);

        $project->attachTag(createServiceTag(ServiceAreas::MANAGEMENT_ERP_CRM, 'CRM'));

        livewire(Services::class)
            ->assertOk()
            ->assertSee($project->title)
            ->assertSeeHtml(route('project.show', $project->slug))
            ->assertSeeHtml('data-pan="cta-services-custom-software-development"')
            ->assertDontSeeHtml('data-pan="cta-services-area-2"');
    });
});

it('carries the translated texts the hub script needs', function (): void {
    livewire(Services::class)
        ->assertOk()
        ->assertSeeHtml('data-config=')
        ->assertSee(__('a 15-minute consultation'));
});

it('leaves the page indexable', function (): void {
    assertPageIsIndexable('services');
});
