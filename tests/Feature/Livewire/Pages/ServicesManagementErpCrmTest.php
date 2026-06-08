<?php

declare(strict_types=1);

use App\Livewire\Pages\ServicesManagementErpCrm;
use App\Models\Project;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;

use function Pest\Livewire\livewire;

beforeEach(fn () => $this->withoutVite());

it('renders the hero with the area eyebrow and sub-service pills', function () {
    livewire(ServicesManagementErpCrm::class)
        ->assertOk()
        ->assertSeeHtml('data-pan="section-impression-services-custom-software-development"')
        ->assertSee(__('Area 01 — Management software · ERP · CRM'))
        ->assertSee(__('Tailor-made management software'))
        ->assertSee(__('ERP systems'))
        ->assertSee(__('CRM & customer management'))
        ->assertSeeHtml('data-pan="cta-services-custom-software-development-consultation"');
});

it('lays out the pains, sub-services, process and FAQ sections', function () {
    livewire(ServicesManagementErpCrm::class)
        ->assertOk()
        ->assertSee(__('This is for you if…'))
        ->assertSee(__('You live inside scattered spreadsheets.'))
        ->assertSee(__('What I build<br>in this area'), escape: false)
        ->assertSee(__('Roles and permissions for each team'))
        ->assertSee(__('From your process<br>to the software, in 5 steps'), escape: false)
        ->assertSee(__('Analysis'))
        ->assertSee(__('Frequently asked questions'))
        ->assertSee(__('Who owns the software code?'));
});

it('lists a commercial stack: PostgreSQL only, no React or MySQL', function () {
    // Guards the chat-4 decision: PostgreSQL only, React removed.
    livewire(ServicesManagementErpCrm::class)
        ->assertOk()
        ->assertSee('PostgreSQL')
        ->assertSee('Vue.js')
        ->assertDontSee('React')
        ->assertDontSee('MySQL');
});

it('frames the AI band around capability, never speed', function () {
    // The AI copy must never promise "faster" delivery (chat-4 hard rule).
    livewire(ServicesManagementErpCrm::class)
        ->assertOk()
        ->assertSee(__('AI built into your processes.'))
        ->assertSee(__('I build more ambitious, more polished systems, without ballooning cost and complexity.'));
});

it('hides the case study when there is no published project', function () {
    livewire(ServicesManagementErpCrm::class)
        ->assertOk()
        ->assertDontSeeHtml('id="case"')
        ->assertDontSeeHtml('data-pan="cta-services-custom-software-development-case"');
});

it('surfaces a featured project as the case study and the hero project link', function () {
    $project = Project::factory()->published()->featured()->create();

    livewire(ServicesManagementErpCrm::class)
        ->assertOk()
        ->assertSeeHtml('id="case"')
        ->assertSee($project->title)
        ->assertSee(__('Real case — Management software & CRM'))
        ->assertSeeHtml(route('project.show', $project->slug))
        ->assertSeeHtml('data-pan="cta-services-custom-software-development-case"')
        ->assertSeeHtml('data-pan="cta-services-custom-software-development-project"');
});

it('closes with the final call to action', function () {
    livewire(ServicesManagementErpCrm::class)
        ->assertOk()
        ->assertSee(__('Shall we bring order<br>to your processes?'), escape: false)
        ->assertSee(__('Book a call'))
        ->assertSee(__('Back to services'))
        ->assertSeeHtml('data-pan="cta-services-custom-software-development-contacts"')
        ->assertSeeHtml('data-pan="cta-services-custom-software-development-back"');
});

it('is reachable at its localized route and emits a meta description', function () {
    $response = $this->withoutMiddleware([
        LaravelLocalizationRedirectFilter::class,
        LocaleSessionRedirect::class,
    ])->get(route('services.management_erp_crm'));

    $response->assertOk()
        ->assertSee('<meta name="description" content="'.e(__('Tailor-made management software, ERP and CRM for SMEs: one system stitched onto your processes. Workflow automation, real-time data, 100% your code. No more scattered spreadsheets and double data entry.')).'">', escape: false);
});
