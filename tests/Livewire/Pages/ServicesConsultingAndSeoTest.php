<?php

declare(strict_types=1);

use App\Enums\ServiceAreas;
use App\Livewire\Pages\ServicesConsultingAndSeo;

use function Pest\Livewire\livewire;

it('lays out the hero, pains, offers and FAQ sections', function (): void {
    livewire(ServicesConsultingAndSeo::class)
        ->assertOk()
        ->assertSeeHtml('data-pan="section-impression-services-consulting-and-seo"')
        ->assertSeeHtml('data-pan="cta-services-consulting-and-seo-consultation"')
        ->assertSeeHtml('data-pan="cta-services-consulting-and-seo-back"')
        ->assertSee(__('You know you need digital, but not where to start.'))
        ->assertSee(__('Strategic consulting'))
        ->assertSee(__('How does a consultation work?'))
        ->assertSeeHtml('application/ld+json');
});

it('uses the rank animation for the hero', function (): void {
    livewire(ServicesConsultingAndSeo::class)
        ->assertOk()
        ->assertSeeHtml('data-effect="service-landing-hero" data-mode="rank"');
});

it('hides the projects showcase when no project is tagged with this service', function (): void {
    assertServiceLandingHidesShowcaseWithoutTaggedProjects(ServicesConsultingAndSeo::class, ServiceAreas::CONSULTING_AND_SEO);
});

it('showcases published projects tagged with this service', function (): void {
    assertServiceLandingShowcasesTaggedProjects(ServicesConsultingAndSeo::class, ServiceAreas::CONSULTING_AND_SEO, 'SEO');
});

it('is reachable at its localized route and emits a meta description', function (): void {
    assertPageEmitsMetaDescription(
        ServiceAreas::CONSULTING_AND_SEO->getRouteName(),
        __('Digital and SEO consulting for SMEs: understand what you really need before building. Strategy, independent technology choices and solid foundations to get you found on Google. Support for your team, when needed.'),
    );
});
