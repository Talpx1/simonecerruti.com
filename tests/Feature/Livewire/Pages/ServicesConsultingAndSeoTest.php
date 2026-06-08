<?php

declare(strict_types=1);

use App\Livewire\Pages\ServicesConsultingAndSeo;
use App\Models\Project;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;

use function Pest\Livewire\livewire;

beforeEach(fn () => $this->withoutVite());

it('renders the hero with the area eyebrow and sub-service pills', function () {
    livewire(ServicesConsultingAndSeo::class)
        ->assertOk()
        ->assertSeeHtml('data-pan="section-impression-services-consulting-and-seo"')
        ->assertSee(__('Area 03 — Consulting · SEO'))
        ->assertSee(__('Strategic consulting'))
        ->assertSee(__('SEO & positioning'))
        ->assertSee(__('Support for your team'))
        ->assertSeeHtml('data-pan="cta-services-consulting-and-seo-consultation"');
});

it('lays out the pains, sub-services, process and FAQ sections', function () {
    livewire(ServicesConsultingAndSeo::class)
        ->assertOk()
        ->assertSee(__('This is for you if…'))
        ->assertSee(__('You have an idea but don\'t know where to start.'))
        ->assertSee(__('What I offer<br>in this area'), escape: false)
        ->assertSee(__('Support & audit'))
        ->assertSee(__('From the problem<br>to the right path, in 5 steps'), escape: false)
        ->assertSee(__('Listening'))
        ->assertSee(__('Frequently asked questions'))
        ->assertSee(__('How does a consultation work?'));
});

it('lists the method toolkit and the strategy-first values', function () {
    livewire(ServicesConsultingAndSeo::class)
        ->assertOk()
        ->assertSee(__('Tools & method'))
        ->assertSee(__('SEO audit'))
        ->assertSee(__('Why start from strategy'))
        ->assertSee(__('Found on Google'));
});

it('frames the AI band around quality, never speed', function () {
    // AI copy must stay tied to quality and never promise speed (persistent project rule).
    livewire(ServicesConsultingAndSeo::class)
        ->assertOk()
        ->assertSee(__('Deeper analysis.'))
        ->assertSee(__('AI suggests, I weigh in: every recommendation stays carefully vetted, for the utmost quality of what I hand over.'));
});

it('hides the case study when there is no published project', function () {
    livewire(ServicesConsultingAndSeo::class)
        ->assertOk()
        ->assertDontSeeHtml('id="case"')
        ->assertDontSeeHtml('data-pan="cta-services-consulting-and-seo-case"')
        ->assertDontSeeHtml('data-pan="cta-services-consulting-and-seo-project"');
});

it('surfaces a featured project as the case study and the hero project link', function () {
    $project = Project::factory()->published()->featured()->create();

    livewire(ServicesConsultingAndSeo::class)
        ->assertOk()
        ->assertSeeHtml('id="case"')
        ->assertSee($project->title)
        ->assertSee(__('Real case — Strategy & SEO'))
        ->assertSeeHtml(route('project.show', $project->slug))
        ->assertSeeHtml('data-pan="cta-services-consulting-and-seo-case"')
        ->assertSeeHtml('data-pan="cta-services-consulting-and-seo-project"');
});

it('closes with the final call to action', function () {
    livewire(ServicesConsultingAndSeo::class)
        ->assertOk()
        ->assertSee(__('Let\'s figure out together<br>where to start?'), escape: false)
        ->assertSee(__('Book a call'))
        ->assertSee(__('Back to services'))
        ->assertSeeHtml('data-pan="cta-services-consulting-and-seo-contacts"')
        ->assertSeeHtml('data-pan="cta-services-consulting-and-seo-back"');
});

it('is reachable at its localized route and emits a meta description', function () {
    $response = $this->withoutMiddleware([
        LaravelLocalizationRedirectFilter::class,
        LocaleSessionRedirect::class,
    ])->get(route('services.consulting_and_seo'));

    $response->assertOk()
        ->assertSee('<meta name="description" content="'.e(__('Digital and SEO consulting for SMEs: understand what you really need before building. Strategy, independent technology choices and solid foundations to get you found on Google. Support for your team, when needed.')).'">', escape: false);
});
