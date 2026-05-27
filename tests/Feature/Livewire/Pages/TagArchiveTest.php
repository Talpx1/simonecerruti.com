<?php

declare(strict_types=1);

use App\Enums\TagTypes;
use App\Livewire\Pages\TagArchive;
use App\Models\BlogArticle;
use App\Models\Project;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Spatie\Tags\Tag;

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
    expect(fn () => livewire(TagArchive::class, ['slug' => 'does-not-exist']))
        ->toThrow(ModelNotFoundException::class);
});
