<?php

declare(strict_types=1);

use App\Enums\TagTypes;
use App\Models\Client;
use App\Models\Project;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

describe('wherePublished scope', function () {
    it('only returns published projects', function () {
        $published = Project::factory()->published()->create();
        $unpublished = Project::factory()->unpublished()->create();

        $result = Project::query()->wherePublished()->get();

        expect($result->contains($published))->toBeTrue()
            ->and($result->contains($unpublished))->toBeFalse();
    });
});

describe('casts', function () {
    it('casts links to a collection', function () {
        $project = Project::factory()->create([
            'links' => [['url' => 'https://example.com']],
        ]);

        expect($project->links)->toBeInstanceOf(Collection::class)
            ->and($project->links->first()['url'])->toBe('https://example.com');
    });
});

describe('featured_image_url attribute', function () {
    it('falls back to the bundled placeholder when no media is attached', function () {
        $project = Project::factory()->create();

        expect($project->featured_image_url)->toBe(asset('images/fallback.jpg'));
    });

    it('returns a media url when a featured image is attached', function () {
        Storage::fake('public');

        $project = Project::factory()->create();
        $project->addMedia(UploadedFile::fake()->image('featured.jpg', 1920, 1080))
            ->toMediaCollection('featured_image');

        expect($project->refresh()->featured_image_url)->not->toBe(asset('images/fallback.jpg'));
    });
});

describe('short_description_or_excerpt attribute', function () {
    it('returns the short description when present', function () {
        $project = Project::factory()->create();

        withLocale('en', function () use ($project) {
            expect($project->short_description_or_excerpt)->toBe($project->short_description);
        });
    });
});

describe('relations', function () {
    describe('client', function () {
        it('belongs to a client', function () {
            $client = Client::factory()->create();
            $project = Project::factory()->for($client)->create();

            expect($project->load('client')->client)->toBe($client);
        });

        it('can have no client', function () {
            $project = Project::factory()->withoutClient()->create();

            expect($project->client_id)->toBeNull()
                ->and($project->load('client')->client)->toBeNull();
        });
    });
});

describe('story attributes', function () {
    it('are nullable', function () {
        $project = Project::factory()->withoutClient()->create()->refresh();

        expect($project->starting_point)->toBeNull()
            ->and($project->goal)->toBeNull()
            ->and($project->outcome)->toBeNull()
            ->and($project->kind)->toBeNull()
            ->and($project->features)->toBeNull()
            ->and($project->home_summary)->toBeNull();
    });

    it('are translatable per locale', function () {
        $project = Project::factory()->create();
        $project->setTranslation('goal', 'it', 'Obiettivo')->setTranslation('goal', 'en', 'Goal')->save();

        $project->refresh();

        expect(withLocale('it', fn () => $project->goal))->toBe('Obiettivo')
            ->and(withLocale('en', fn () => $project->goal))->toBe('Goal');
    });

    it('round-trips the features list per locale', function () {
        $it_features = [['title' => 'Ordini', 'description' => 'Gestione ordini'], ['title' => 'Report', 'description' => 'Report mensili']];
        $en_features = [['title' => 'Orders', 'description' => 'Order management']];
        $project = Project::factory()->create();
        $project->setTranslation('features', 'it', $it_features)->setTranslation('features', 'en', $en_features)->save();

        $project = Project::query()->findOrFail($project->id);

        expect(withLocale('it', fn () => $project->features))->toBe($it_features)
            ->and(withLocale('en', fn () => $project->features))->toBe($en_features);
    });
});

describe('kind_or_tag_names attribute', function () {
    it('returns the kind when set', function () {
        $project = Project::factory()->withStory()->create();
        $project->attachTag('Laravel', TagTypes::TAG->value);
        $project->setTranslation('kind', 'en', 'Custom ERP')->save();

        expect(withLocale('en', fn () => $project->refresh()->load('tags')->kind_or_tag_names))->toBe('Custom ERP');
    });

    it('falls back to the tag names joined with a plus', function () {
        $project = Project::factory()->create();
        $names = withLocale('en', function () use ($project): string {
            $project->attachTags(['Laravel', 'Vue'], TagTypes::TAG->value);
            $project->attachTag('Ignored', TagTypes::BLOG_CATEGORY->value);

            return $project->refresh()->load('tags')->kind_or_tag_names;
        });

        expect($names)->toContain(' + ')
            ->and($names)->toContain('Laravel')
            ->and($names)->toContain('Vue')
            ->and($names)->not->toContain('Ignored');
    });

    it('is empty without kind and tags', function () {
        $project = Project::factory()->create()->load('tags');

        expect(withLocale('en', fn () => $project->kind_or_tag_names))->toBeEmpty();
    });
});

describe('seo variables', function () {
    it('exposes the client name', function () {
        $project = Project::factory()->for(Client::factory()->create(['name' => 'Acme']))->create();
        $orphan = Project::factory()->withoutClient()->create();

        $variables = fn (Project $project): array => (fn (): array => $this->seoVariables())->call($project->load('tags'));

        expect($variables($project)['client'])->toBe('Acme')
            ->and($variables($orphan)['client'])->toBeEmpty();
    });
});

describe('factory', function () {
    it('fills every story field with the withStory state', function () {
        $project = Project::factory()->withStory()->create()->refresh();

        expect($project->getTranslation('starting_point', 'en'))->not->toBeEmpty()
            ->and($project->getTranslation('goal', 'it'))->not->toBeEmpty()
            ->and($project->getTranslation('outcome', 'en'))->not->toBeEmpty()
            ->and($project->getTranslation('kind', 'it'))->not->toBeEmpty()
            ->and($project->getTranslation('home_summary', 'en'))->not->toBeEmpty()
            ->and($project->getTranslation('features', 'en'))->toBeArray()->not->toBeEmpty();
    });

    it('removes the client with the withoutClient state', function () {
        expect(Project::factory()->withoutClient()->create()->client_id)->toBeNull();
    });
});
