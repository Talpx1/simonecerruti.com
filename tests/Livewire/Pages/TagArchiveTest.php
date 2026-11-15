<?php

declare(strict_types=1);

use App\Enums\ServiceAreas;
use App\Enums\TagTypes;
use App\Livewire\Pages\TagArchive;
use App\Models\BlogArticle;
use App\Models\Project;
use App\Models\Tag;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->withoutVite();
    app()->setLocale('en');
});

it('shows articles and projects tagged with the given tag', function () {
    $tag = Tag::findOrCreate('My Tag', TagTypes::TAG->value);

    $article = BlogArticle::factory()->published()->create();
    $article->attachTag($tag);

    $project = Project::factory()->published()->create();
    $project->attachTag($tag);

    $component = livewire(TagArchive::class, ['slug' => $tag->slug])
        ->assertOk();

    expect($component->instance()->articles_total)->toBe(1)
        ->and($component->instance()->projects_total)->toBe(1)
        ->and($component->instance()->articles->pluck('id'))->toContain($article->id)
        ->and($component->instance()->projects->pluck('id'))->toContain($project->id);
});

it('returns a 404 for an unknown tag slug', function () {
    livewire(TagArchive::class, ['slug' => 'does-not-exist'])
        ->assertNotFound();
});

it('shows the tag description and the service link when set', function () {
    $tag = Tag::findOrCreate('CRM', TagTypes::TAG->value);
    $tag->setTranslation('description', 'en', 'Clients in one place.')->update(['service' => ServiceAreas::MANAGEMENT_ERP_CRM]);

    livewire(TagArchive::class, ['slug' => $tag->slug])
        ->assertSee('Clients in one place.')
        ->assertSee('The service')
        ->assertSeeHtml(route(ServiceAreas::MANAGEMENT_ERP_CRM->getRouteName()));
});

it('hides the service link and description when the tag has none', function () {
    $tag = Tag::findOrCreate('CRM', TagTypes::TAG->value);

    livewire(TagArchive::class, ['slug' => $tag->slug])
        ->assertDontSee('The service');
});

it('lists the tagged projects and links every type tag in the cloud', function () {
    $tag = Tag::findOrCreate('CRM', TagTypes::TAG->value);
    $other = Tag::findOrCreate('Open source', TagTypes::TAG->value);
    $project = Project::factory()->published()->create(['title' => ['en' => 'Tagged Work', 'it' => 'Lavoro']]);
    $project->attachTag($tag);

    livewire(TagArchive::class, ['slug' => $tag->slug])
        ->assertSee('Tagged Work')
        ->assertSeeHtml(route('tag_archive', ['slug' => $other->slug]))
        ->assertSeeHtml('aria-current="page"');
});
