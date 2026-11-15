<?php

declare(strict_types=1);

use App\Enums\ServiceAreas;
use App\Livewire\Pages\ServicesWebEcommercePlatforms;

use function Pest\Livewire\livewire;

it('lays out the hero, pains, offers and FAQ sections', function (): void {
    livewire(ServicesWebEcommercePlatforms::class)
        ->assertOk()
        ->assertSeeHtml('data-pan="section-impression-services-web-development"')
        ->assertSeeHtml('data-pan="cta-services-web-development-consultation"')
        ->assertSeeHtml('data-pan="cta-services-web-development-back"')
        ->assertSee(__('You have a website, but nobody contacts you through it.'))
        ->assertSee(__('E-commerce'))
        ->assertSee(__('Platforms and web apps'))
        ->assertSee(__('Who owns the website in the end?'))
        ->assertSeeHtml('application/ld+json');
});

it('uses the flow animation for the hero', function (): void {
    livewire(ServicesWebEcommercePlatforms::class)
        ->assertOk()
        ->assertSeeHtml('data-effect="service-landing-hero" data-mode="flow"');
});

it('hides the projects showcase when no project is tagged with this service', function (): void {
    assertServiceLandingHidesShowcaseWithoutTaggedProjects(ServicesWebEcommercePlatforms::class, ServiceAreas::WEB_ECOMMERCE_PLATFORMS);
});

it('showcases published projects tagged with this service', function (): void {
    assertServiceLandingShowcasesTaggedProjects(ServicesWebEcommercePlatforms::class, ServiceAreas::WEB_ECOMMERCE_PLATFORMS, 'Website')
        ->assertSeeHtml(route('tag_archive', ['slug' => 'website']));
});

it('is reachable at its localized route and emits a meta description', function (): void {
    assertPageEmitsMetaDescription(
        ServiceAreas::WEB_ECOMMERCE_PLATFORMS->getRouteName(),
        __('Tailor-made websites, e-commerce and platforms for SMEs: a site that brings in customers, sells and grows with your business. Fast, ready for Google and connected to your tools. 100% your code.'),
    );
});
