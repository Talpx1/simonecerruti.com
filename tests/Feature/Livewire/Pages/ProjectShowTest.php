<?php

declare(strict_types=1);

use App\Livewire\Pages\Project\ProjectShow;
use App\Models\Project;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;

use function Pest\Livewire\livewire;

beforeEach(fn () => $this->withoutVite());

it('renders a project', function () {
    $project = Project::factory()->published()->create();

    $component = livewire(ProjectShow::class, ['project' => $project])
        ->assertOk();

    expect($component->instance()->project->id)->toBe($project->id);
});

it('renders the full SEO head, including the sitewide schema nodes', function () {
    $project = Project::factory()->published()->create(['title' => 'My SEO Project']);
    $url = route('project.show', $project->getTranslation('slug', app()->getLocale()));

    $html = $this->withoutMiddleware([
        LaravelLocalizationRedirectFilter::class,
        LocaleSessionRedirect::class,
    ])->get($url)->assertOk()->getContent();

    expect($html)
        ->toContain('<title>My SEO Project | '.config('app.name').'</title>')
        ->toContain('<link rel="canonical"')
        ->toContain('property="og:title"')
        ->toContain('name="twitter:card"')
        // The page node and the prepended sitewide nodes share one @graph.
        ->toContain('"@type":"CreativeWork"')
        ->toContain('"@type":"WebSite"')
        ->and(substr_count((string) $html, '<title'))->toBe(1)
        ->and(substr_count((string) $html, 'application/ld+json'))->toBe(1);
});
