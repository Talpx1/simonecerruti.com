<?php

declare(strict_types=1);

use App\Enums\ServiceAreas;
use App\Enums\TagTypes;
use App\Livewire\Pages\ServicesManagementErpCrm;
use App\Models\Project;
use App\Models\Tag;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;

use function Pest\Livewire\livewire;

beforeEach(fn () => $this->withoutVite());

it('lays out the hero, pains, offers and FAQ sections', function () {
    livewire(ServicesManagementErpCrm::class)
        ->assertOk()
        ->assertSeeHtml('data-pan="section-impression-services-custom-software-development"')
        ->assertSeeHtml('data-pan="cta-services-custom-software-development-consultation"')
        ->assertSeeHtml('data-pan="cta-services-custom-software-development-back"')
        ->assertSee(__('Excel files everywhere, each with its own version of the truth.'))
        ->assertSee(__('Tailor-made management software'))
        ->assertSee(__('Who owns the software in the end?'))
        ->assertSeeHtml('application/ld+json');
});

it('marks the current service in the services navigation', function () {
    livewire(ServicesManagementErpCrm::class)
        ->assertOk()
        ->assertSeeHtml('href="'.route('services.web_ecommerce_platforms').'"')
        ->assertSeeHtml('href="'.route('services.consulting_and_seo').'"')
        ->assertSeeHtml('class="is-current" aria-current="page"');
});

it('lists a commercial stack: PostgreSQL only, no React or MySQL', function () {
    livewire(ServicesManagementErpCrm::class)
        ->assertOk()
        ->assertSee('PostgreSQL')
        ->assertSee('Vue')
        ->assertDontSee('React')
        ->assertDontSee('MySQL');
});

it('hides the projects showcase when no project is tagged with this service', function () {
    $other_area_project = Project::factory()->published()->create();
    $tag = Tag::findOrCreate('Website', TagTypes::TAG->value);
    $tag->update(['service' => ServiceAreas::WEB_ECOMMERCE_PLATFORMS]);
    $other_area_project->attachTag($tag);

    livewire(ServicesManagementErpCrm::class)
        ->assertOk()
        ->assertDontSee(__('Already<br>in use'), escape: false)
        ->assertDontSee($other_area_project->title);
});

it('showcases published projects tagged with this service', function () {
    $tag = Tag::findOrCreate('CRM', TagTypes::TAG->value);
    $tag->update(['service' => ServiceAreas::MANAGEMENT_ERP_CRM]);
    $project = Project::factory()->published()->create();
    $project->attachTag($tag);
    $unpublished = Project::factory()->unpublished()->create();
    $unpublished->attachTag($tag);

    livewire(ServicesManagementErpCrm::class)
        ->assertOk()
        ->assertSee(__('Already<br>in use'), escape: false)
        ->assertSee($project->title)
        ->assertDontSee($unpublished->title)
        ->assertSeeHtml(route('project.show', $project->slug))
        ->assertSeeHtml('data-pan="cta-services-custom-software-development-project"');
});

it('is reachable at its localized route and emits a meta description', function () {
    $response = $this->withoutMiddleware([
        LaravelLocalizationRedirectFilter::class,
        LocaleSessionRedirect::class,
    ])->get(route('services.management_erp_crm'));

    $response->assertOk()
        ->assertSee('<meta name="description" content="'.e(__('Tailor-made management software, ERP and CRM for SMEs: one system stitched onto your processes. Workflow automation, real-time data, 100% your code. No more scattered spreadsheets and double data entry.')).'">', escape: false);
});
