<?php

declare(strict_types=1);

use App\Enums\ServiceAreas;
use App\Enums\TagTypes;
use App\Livewire\Pages\ServicesWebEcommercePlatforms;
use App\Models\Project;
use App\Models\Tag;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;

use function Pest\Livewire\livewire;

beforeEach(fn () => $this->withoutVite());

it('lays out the hero, pains, offers and FAQ sections', function () {
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

it('uses the flow animation for the hero', function () {
    livewire(ServicesWebEcommercePlatforms::class)
        ->assertOk()
        ->assertSeeHtml('data-fx="svc" data-mode="flow"');
});

it('hides the projects showcase when no project is tagged with this service', function () {
    $other_area_project = Project::factory()->published()->create();
    $tag = Tag::findOrCreate('CRM', TagTypes::TAG->value);
    $tag->update(['service' => ServiceAreas::MANAGEMENT_ERP_CRM]);
    $other_area_project->attachTag($tag);

    livewire(ServicesWebEcommercePlatforms::class)
        ->assertOk()
        ->assertDontSee(__('Already<br>in use'), escape: false)
        ->assertDontSee($other_area_project->title);
});

it('showcases published projects tagged with this service', function () {
    $tag = Tag::findOrCreate('Website', TagTypes::TAG->value);
    $tag->update(['service' => ServiceAreas::WEB_ECOMMERCE_PLATFORMS]);
    $project = Project::factory()->published()->create();
    $project->attachTag($tag);

    livewire(ServicesWebEcommercePlatforms::class)
        ->assertOk()
        ->assertSee($project->title)
        ->assertSeeHtml(route('project.show', $project->slug))
        ->assertSeeHtml(route('tag_archive', ['slug' => $tag->slug]));
});

it('is reachable at its localized route and emits a meta description', function () {
    $response = $this->withoutMiddleware([
        LaravelLocalizationRedirectFilter::class,
        LocaleSessionRedirect::class,
    ])->get(route('services.web_ecommerce_platforms'));

    $response->assertOk()
        ->assertSee('<meta name="description" content="'.e(__('Tailor-made websites, e-commerce and platforms for SMEs: a site that brings in customers, sells and grows with your business. Fast, ready for Google and connected to your tools. 100% your code.')).'">', escape: false);
});
