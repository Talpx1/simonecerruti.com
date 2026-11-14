<?php

declare(strict_types=1);

use App\Enums\ServiceAreas;
use App\Enums\TagTypes;
use App\Livewire\Pages\Services;
use App\Models\Project;
use App\Models\Tag;

use function Pest\Livewire\livewire;

beforeEach(fn () => $this->withoutVite());

it('renders the hero, the goals and the three service cards', function () {
    livewire(Services::class)
        ->assertOk()
        ->assertSeeHtml('data-pan="section-impression-services-hero"')
        ->assertSeeHtml('data-pan="cta-services-contacts"')
        ->assertSeeHtml('data-fx="routes"')
        ->assertSeeHtml('data-fx="services-hub"')
        ->assertSeeHtml('id="area-management"')
        ->assertSeeHtml('id="area-web"')
        ->assertSeeHtml('id="area-consulting"')
        ->assertSee(__('Manage the company better'))
        ->assertSee(__('Sell online'));
});

it('links every card to its dedicated landing page', function (ServiceAreas $area) {
    livewire(Services::class)
        ->assertOk()
        ->assertSeeHtml('href="'.route($area->getRouteName()).'"');
})->with(ServiceAreas::cases());

it('shows no example project when nothing is tagged with a service', function () {
    Project::factory()->published()->featured()->create();

    livewire(Services::class)
        ->assertOk()
        ->assertDontSeeHtml('class="sex"');
});

it('shows the example project of each area from its service tag', function () {
    $tag = Tag::findOrCreate('CRM', TagTypes::TAG->value);
    $tag->update(['service' => ServiceAreas::MANAGEMENT_ERP_CRM]);
    $project = Project::factory()->published()->create();
    $project->attachTag($tag);

    livewire(Services::class)
        ->assertOk()
        ->assertSee($project->title)
        ->assertSeeHtml(route('project.show', $project->slug))
        ->assertSeeHtml('data-pan="cta-services-custom-software-development"')
        ->assertDontSeeHtml('data-pan="cta-services-area-2"');
});

it('carries the translated texts the hub script needs', function () {
    livewire(Services::class)
        ->assertOk()
        ->assertSeeHtml('data-config=')
        ->assertSee(__('a 15-minute consultation'));
});
