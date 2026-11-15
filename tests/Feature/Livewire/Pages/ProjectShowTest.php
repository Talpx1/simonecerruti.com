<?php

declare(strict_types=1);

use App\Enums\TagTypes;
use App\Livewire\Pages\Project\ProjectShow;
use App\Models\Client;
use App\Models\Project;
use App\Models\Tag;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->withoutVite();
    app()->setLocale('en');
});

it('renders a project', function () {
    $project = Project::factory()->published()->create();

    $component = livewire(ProjectShow::class, ['project' => $project])
        ->assertOk();

    expect($component->instance()->project->id)->toBe($project->id);
});

it('renders no previous and next navigation when there is only one project', function () {
    $project = Project::factory()->published()->create();

    livewire(ProjectShow::class, ['project' => $project])
        ->assertDontSeeHtml('previous-next-navigation');
});

it('links only the other project in the previous and next navigation', function () {
    $project = Project::factory()->published()->create(['title' => 'Current Unique Title']);
    Project::factory()->published()->create(['title' => 'Other Project']);

    $html = livewire(ProjectShow::class, ['project' => $project])->html();

    \preg_match('/<nav class="previous-next-navigation".*?<\/nav>/s', $html, $navigation);

    expect($html)->toContain('Other Project')
        ->and($navigation)->not->toBeEmpty()
        ->and($navigation[0])->toContain('Other Project')
        ->and($navigation[0])->not->toContain('Current Unique Title');
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

it('renders the story, outcome, features, client and tag links when filled', function () {
    $client = Client::factory()->create(['description' => ['en' => 'A historic driving school', 'it' => 'Autoscuola']]);
    $project = Project::factory()->published()->withStory()->create(['client_id' => $client->id]);
    $project->attachTag(Tag::findOrCreate('CRM', TagTypes::TAG->value));
    $project->attachTag(Tag::findOrCreate('Laravel', TagTypes::TECHNOLOGY->value));
    $feature = $project->features[0];

    livewire(ProjectShow::class, ['project' => $project])
        ->assertSee('The starting point.')
        ->assertSee($project->starting_point)
        ->assertSee('The goal.')
        ->assertSee($project->outcome)
        ->assertSee($feature['title'])
        ->assertSee($feature['description'])
        ->assertSee('A historic driving school')
        ->assertSee('CRM')
        ->assertSee('Laravel')
        ->assertSeeHtml(route('tag_archive', ['slug' => 'crm']));
});

it('hides every optional section when the project has no extra data', function () {
    $project = Project::factory()->published()->withoutClient()->create(['links' => []]);

    livewire(ProjectShow::class, ['project' => $project])
        ->assertDontSee('The starting point.')
        ->assertDontSee('The goal.')
        ->assertDontSee('Features')
        ->assertDontSee('Client')
        ->assertDontSee('Stack');
});

it('links to the previous and next projects in list order', function () {
    $featured = Project::factory()->published()->featured()->create(['created_at' => now()->subDays(5)]);
    $newest = Project::factory()->published()->create(['created_at' => now()->subDay()]);
    $oldest = Project::factory()->published()->create(['created_at' => now()->subDays(10)]);

    livewire(ProjectShow::class, ['project' => $newest])
        ->assertSeeHtml(route('project.show', $featured->slug))
        ->assertSeeHtml(route('project.show', $oldest->slug));
});

it('hides the previous and next navigation when it is the only project', function () {
    $project = Project::factory()->published()->create();

    livewire(ProjectShow::class, ['project' => $project])
        ->assertDontSee('Previous work');
});
