<?php

declare(strict_types=1);

use App\Enums\ServiceAreas;
use App\Enums\TagTypes;
use App\Livewire\Pages\ServicesConsultingAndSeo;
use App\Models\Project;
use App\Models\Tag;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;

use function Pest\Livewire\livewire;

beforeEach(fn () => $this->withoutVite());

it('lays out the hero, pains, offers and FAQ sections', function () {
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

it('uses the rank animation for the hero', function () {
    livewire(ServicesConsultingAndSeo::class)
        ->assertOk()
        ->assertSeeHtml('data-effect="service-landing-hero" data-mode="rank"');
});

it('hides the projects showcase when no project is tagged with this service', function () {
    $other_area_project = Project::factory()->published()->create();
    $tag = Tag::findOrCreate('Website', TagTypes::TAG->value);
    $tag->update(['service' => ServiceAreas::WEB_ECOMMERCE_PLATFORMS]);
    $other_area_project->attachTag($tag);

    livewire(ServicesConsultingAndSeo::class)
        ->assertOk()
        ->assertDontSee(__('Already<br>in use'), escape: false)
        ->assertDontSee($other_area_project->title);
});

it('showcases published projects tagged with this service', function () {
    $tag = Tag::findOrCreate('SEO', TagTypes::TAG->value);
    $tag->update(['service' => ServiceAreas::CONSULTING_AND_SEO]);
    $project = Project::factory()->published()->create();
    $project->attachTag($tag);

    livewire(ServicesConsultingAndSeo::class)
        ->assertOk()
        ->assertSee($project->title)
        ->assertSeeHtml(route('project.show', $project->slug));
});

it('is reachable at its localized route and emits a meta description', function () {
    $response = $this->withoutMiddleware([
        LaravelLocalizationRedirectFilter::class,
        LocaleSessionRedirect::class,
    ])->get(route('services.consulting_and_seo'));

    $response->assertOk()
        ->assertSee('<meta name="description" content="'.e(__('Digital and SEO consulting for SMEs: understand what you really need before building. Strategy, independent technology choices and solid foundations to get you found on Google. Support for your team, when needed.')).'">', escape: false);
});
