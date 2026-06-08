<?php

declare(strict_types=1);

use App\Livewire\Pages\Services;
use App\Models\BlogArticle;
use App\Models\Project;

use function Pest\Livewire\livewire;

beforeEach(fn () => $this->withoutVite());

it('renders with no featured content', function () {
    livewire(Services::class)
        ->assertOk()
        ->assertSeeHtml('data-pan="section-impression-services-hero"')
        ->assertDontSeeHtml('cta-services-custom-software-development')
        ->assertDontSee(__('Real cases & insights'));
});

it('routes each hero goal to its matching area anchor', function () {
    livewire(Services::class)
        ->assertOk()
        ->assertSeeHtml('id="area-01"')
        ->assertSeeHtml('id="area-02"')
        ->assertSeeHtml('id="area-03"')
        ->assertSeeHtml('href="#area-01"')
        ->assertSeeHtml('href="#area-02"')
        ->assertSeeHtml('href="#area-03"');
});

it('links the Area 01 button to the dedicated landing page', function () {
    livewire(Services::class)
        ->assertOk()
        ->assertSeeHtml(route('services.management_erp_crm'));
});

it('links the Area 02 button to the dedicated landing page', function () {
    livewire(Services::class)
        ->assertOk()
        ->assertSeeHtml(route('services.web_ecommerce_platforms'));
});

it('surfaces the featured project in the Area 01 contextual CTA', function () {
    $project = Project::factory()->published()->featured()->create();

    livewire(Services::class)
        ->assertOk()
        ->assertSee($project->title)
        ->assertSee(__('Project:'))
        ->assertSeeHtml(route('project.show', $project->slug))
        ->assertSeeHtml('data-pan="cta-services-custom-software-development"');
});

it('keeps the Area 02 and Area 03 contextual CTAs hidden', function () {
    Project::factory()->published()->featured()->create();

    livewire(Services::class)
        ->assertOk()
        ->assertDontSeeHtml('cta-services-area-2')
        ->assertDontSeeHtml('cta-services-area-3');
});

it('frames the AI section around quality, not speed', function () {
    // The AI band must stay framed around quality/reliability, never speed.
    // Asserting the exact reworded copy is the real guard — reverting either
    // line to a speed-framed version drops these strings and fails here. A
    // blanket "no speed words" check is unusable: Area 02 legitimately uses
    // "Veloce" for the website's own performance.
    livewire(Services::class)
        ->assertOk()
        ->assertSee(__('I build AI into the software I create — and I use it every day to develop better, more solid and reliable apps.'))
        ->assertSee(__('I build more robust, reliable software — without compromising on code quality.'));
});

it('showcases featured projects and the latest article as cards', function () {
    $projects = Project::factory()->published()->featured()->count(2)->create();
    $article = BlogArticle::factory()->published()->featured()->create();

    $component = livewire(Services::class)
        ->assertOk()
        ->assertSee(__('Real cases & insights'))
        ->assertSee(__('All projects'))
        ->assertSee($article->title)
        ->assertSeeHtml(route('blog_article.show', $article->slug))
        ->assertDontSee($article->published_at->format('d.m.Y'));

    foreach ($projects as $project) {
        $component->assertSee($project->title);
    }
});

it('renders the final call to action', function () {
    livewire(Services::class)
        ->assertOk()
        ->assertSee(__('Not sure where to start?'))
        ->assertSee(__('Book a call'))
        ->assertSeeHtml('data-pan="cta-services-contacts"')
        ->assertSeeHtml('data-pan="cta-services-projects"');
});
