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
        ->assertDontSeeHtml('cta-services-area-1')
        ->assertDontSee(__('Real cases & insights'));
});

it('surfaces the featured project in the Area 01 contextual CTA', function () {
    $project = Project::factory()->published()->featured()->create();

    livewire(Services::class)
        ->assertOk()
        ->assertSee($project->title)
        ->assertSee(__('Project:'))
        ->assertSeeHtml(route('project.show', $project->slug))
        ->assertSeeHtml('data-pan="cta-services-area-1"');
});

it('keeps the Area 02 and Area 03 contextual CTAs hidden', function () {
    Project::factory()->published()->featured()->create();

    livewire(Services::class)
        ->assertOk()
        ->assertDontSeeHtml('cta-services-area-2')
        ->assertDontSeeHtml('cta-services-area-3');
});

it('frames the AI section around quality, not speed', function () {
    livewire(Services::class)
        ->assertOk()
        ->assertSee(__('I build more robust, reliable software — without compromising on code quality.'))
        ->assertDontSee('più in fretta')
        ->assertDontSee('tempi più corti');
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
        ->assertSeeHtml('data-pan="cta-hero-contacts"')
        ->assertSeeHtml('data-pan="cta-hero-projects"');
});
