<?php

declare(strict_types=1);

use App\Livewire\Pages\ServicesWebEcommercePlatforms;
use App\Models\Project;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;

use function Pest\Livewire\livewire;

beforeEach(fn () => $this->withoutVite());

it('renders the hero with the area eyebrow and sub-service pills', function () {
    livewire(ServicesWebEcommercePlatforms::class)
        ->assertOk()
        ->assertSeeHtml('data-pan="section-impression-services-web-development"')
        ->assertSee(__('Area 02 — Websites · E-commerce · Platforms · Web apps'))
        ->assertSee(__('Websites & landing'))
        ->assertSee(__('E-commerce'))
        ->assertSee(__('Platforms & web apps'))
        ->assertSeeHtml('data-pan="cta-services-web-development-consultation"');
});

it('lays out the pains, sub-services, process and FAQ sections', function () {
    livewire(ServicesWebEcommercePlatforms::class)
        ->assertOk()
        ->assertSee(__('This is for you if…'))
        ->assertSee(__('You have a website that brings in no customers.'))
        ->assertSee(__('What I build<br>in this area'), escape: false)
        ->assertSee(__('Reserved areas and roles for each user'))
        ->assertSee(__('From your goal<br>to the website online, in 5 steps'), escape: false)
        ->assertSee(__('Analysis'))
        ->assertSee(__('Frequently asked questions'))
        ->assertSee(__('Who owns the site once it\'s delivered?'));
});

it('lists a commercial stack: PostgreSQL only, no React or MySQL', function () {
    // Guards the persistent project rule: PostgreSQL only, React removed.
    livewire(ServicesWebEcommercePlatforms::class)
        ->assertOk()
        ->assertSee('PostgreSQL')
        ->assertSee('Vue.js')
        ->assertDontSee('React')
        ->assertDontSee('MySQL');
});

it('frames the AI band around quality, never speed', function () {
    // The AI copy must never promise "faster" delivery (persistent project rule).
    livewire(ServicesWebEcommercePlatforms::class)
        ->assertOk()
        ->assertSee(__('AI built into your products.'))
        ->assertSee(__('I deliver more ambitious, more polished projects, with the utmost attention to the quality of what I hand over.'));
});

it('hides the case study when there is no published project', function () {
    livewire(ServicesWebEcommercePlatforms::class)
        ->assertOk()
        ->assertDontSeeHtml('id="case"')
        ->assertDontSeeHtml('data-pan="cta-services-web-development-case"');
});

it('surfaces a featured project as the case study and the hero project link', function () {
    $project = Project::factory()->published()->featured()->create();

    livewire(ServicesWebEcommercePlatforms::class)
        ->assertOk()
        ->assertSeeHtml('id="case"')
        ->assertSee($project->title)
        ->assertSee(__('Real case — Website & portal'))
        ->assertSeeHtml(route('project.show', $project->slug))
        ->assertSeeHtml('data-pan="cta-services-web-development-case"')
        ->assertSeeHtml('data-pan="cta-services-web-development-project"');
});

it('closes with the final call to action', function () {
    livewire(ServicesWebEcommercePlatforms::class)
        ->assertOk()
        ->assertSee(__('Shall we bring your<br>business online?'), escape: false)
        ->assertSee(__('Book a call'))
        ->assertSee(__('Back to services'))
        ->assertSeeHtml('data-pan="cta-services-web-development-contacts"')
        ->assertSeeHtml('data-pan="cta-services-web-development-back"');
});

it('is reachable at its localized route and emits a meta description', function () {
    $response = $this->withoutMiddleware([
        LaravelLocalizationRedirectFilter::class,
        LocaleSessionRedirect::class,
    ])->get(route('services.web_ecommerce_platforms'));

    $response->assertOk()
        ->assertSee('<meta name="description" content="'.e(__('Tailor-made websites, e-commerce and platforms for SMEs: a site that brings in customers, sells and grows with your business. Fast, ready for Google and connected to your tools. 100% your code.')).'">', escape: false);
});
