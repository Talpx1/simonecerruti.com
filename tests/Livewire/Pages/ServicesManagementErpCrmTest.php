<?php

declare(strict_types=1);

use App\Enums\ServiceAreas;
use App\Livewire\Pages\ServicesManagementErpCrm;

use function Pest\Livewire\livewire;

it('lays out the hero, pains, offers and FAQ sections', function (): void {
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

it('marks the current service in the services navigation', function (): void {
    livewire(ServicesManagementErpCrm::class)
        ->assertOk()
        ->assertSeeHtml('href="'.route(ServiceAreas::WEB_ECOMMERCE_PLATFORMS->getRouteName()).'"')
        ->assertSeeHtml('href="'.route(ServiceAreas::CONSULTING_AND_SEO->getRouteName()).'"')
        ->assertSeeHtml('class="is-current" aria-current="page"');
});

it('lists a commercial stack: PostgreSQL only, no React or MySQL', function (): void {
    livewire(ServicesManagementErpCrm::class)
        ->assertOk()
        ->assertSee('PostgreSQL')
        ->assertSee('Vue')
        ->assertDontSee('React')
        ->assertDontSee('MySQL');
});

it('hides the projects showcase when no project is tagged with this service', function (): void {
    assertServiceLandingHidesShowcaseWithoutTaggedProjects(ServicesManagementErpCrm::class, ServiceAreas::MANAGEMENT_ERP_CRM);
});

it('showcases published projects tagged with this service', function (): void {
    assertServiceLandingShowcasesTaggedProjects(ServicesManagementErpCrm::class, ServiceAreas::MANAGEMENT_ERP_CRM, 'CRM')
        ->assertSeeHtml('data-pan="cta-services-custom-software-development-project"');
});

it('is reachable at its localized route and emits a meta description', function (): void {
    assertPageEmitsMetaDescription(
        ServiceAreas::MANAGEMENT_ERP_CRM->getRouteName(),
        __('Tailor-made management software, ERP and CRM for SMEs: one system stitched onto your processes. Workflow automation, real-time data, 100% your code. No more scattered spreadsheets and double data entry.'),
    );
});

it('leaves the page indexable', function (): void {
    assertPageIsIndexable(ServiceAreas::MANAGEMENT_ERP_CRM->getRouteName());
});
