<?php

declare(strict_types=1);

use App\Enums\TagTypes;
use App\Livewire\Pages\Project\ProjectShow;
use App\Models\Client;
use App\Models\Project;
use App\Models\Tag;

use function Pest\Livewire\livewire;

it('renders a project', function (): void {
    $project = Project::factory()->published()->create();

    $component = livewire(ProjectShow::class, ['project' => $project])
        ->assertOk();

    expect($component->instance()->project->id)->toBe($project->id);
});

it('renders the full SEO head, including the sitewide schema nodes', function (): void {
    $project = Project::factory()->published()->create(['title' => 'My SEO Project']);

    assertPublicPageRendersFullSeoHead(
        route('project.show', $project->getTranslation('slug', app()->getLocale())),
        'My SEO Project',
        'CreativeWork',
    );
});

it('renders the story, outcome, features, client and tag links when filled', function (): void {
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

it('hides every optional section when the project has no extra data', function (): void {
    $project = Project::factory()->published()->withoutClient()->create(['links' => []]);

    livewire(ProjectShow::class, ['project' => $project])
        ->assertDontSee('The starting point.')
        ->assertDontSee('The goal.')
        ->assertDontSee('Features')
        ->assertDontSee('Client')
        ->assertDontSee('Stack');
});

describe('previous and next navigation', function (): void {
    it('renders no navigation when there is only one project', function (): void {
        $project = Project::factory()->published()->create();

        livewire(ProjectShow::class, ['project' => $project])
            ->assertDontSeeHtml('previous-next-navigation')
            ->assertDontSee('Previous work');
    });

    it('links only the other project when there are two', function (): void {
        $project = Project::factory()->published()->create(['title' => 'Current Unique Title']);

        expect(livewire(ProjectShow::class, ['project' => $project])->html())
            ->not->toContain('Other Project');

        Project::factory()->published()->create(['title' => 'Other Project']);

        $html = livewire(ProjectShow::class, ['project' => $project])->html();

        \Safe\preg_match('/<nav class="previous-next-navigation".*?<\/nav>/s', $html, $navigation);

        expect($html)->toContain('Other Project')
            ->and($navigation)->not->toBeEmpty()
            ->and($navigation[0])->toContain('Other Project')
            ->and($navigation[0])->not->toContain('Current Unique Title');
    });

    it('links to the previous and next projects in list order', function (): void {
        $featured = Project::factory()->published()->featured()->create(['created_at' => now()->subDays(5)]);
        $newest = Project::factory()->published()->create(['created_at' => now()->subDay()]);
        $oldest = Project::factory()->published()->create(['created_at' => now()->subDays(10)]);

        livewire(ProjectShow::class, ['project' => $newest])
            ->assertSeeHtml(route('project.show', $featured->slug))
            ->assertSeeHtml(route('project.show', $oldest->slug));
    });
});
