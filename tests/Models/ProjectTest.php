<?php

declare(strict_types=1);

use App\Enums\TagTypes;
use App\Models\Client;
use App\Models\Project;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

describe('attributes', function (): void {
    describe('links', function (): void {
        it('is cast to a collection', function (): void {
            $project = Project::factory()->create([
                'links' => [['url' => 'https://example.com']],
            ]);

            expect($project->links)->toBeInstanceOf(Collection::class)
                ->and($project->links->first()['url'])->toBe('https://example.com');
        });
    });

    describe('featured_image_url', function (): void {
        it('falls back to the bundled placeholder when no media is attached', function (): void {
            $project = Project::factory()->create();

            expect($project->featured_image_url)->toBe(asset('images/fallback.jpg'));
        });

        it('returns a media url when a featured image is attached', function (): void {
            $project = Project::factory()->create();

            expect($project->featured_image_url)->toBe(asset('images/fallback.jpg'))
                ->and($project->addMedia(UploadedFile::fake()->image('featured.jpg', 1920, 1080))->toMediaCollection('featured_image'))->toBeInstanceOf(Media::class)
                ->and($project->refresh()->featured_image_url)->not->toBe(asset('images/fallback.jpg'));
        });
    });

    describe('short_description_or_excerpt', function (): void {
        it('returns the short description when present', function (): void {
            $project = Project::factory()->create();

            runWithLocale('en', function () use ($project): void {
                expect($project->short_description_or_excerpt)->toBe($project->short_description);
            });
        });
    });

    describe('story attributes', function (): void {
        it('are nullable', function (): void {
            $project = Project::factory()->create()->refresh();

            expect($project->starting_point)->toBeNull()
                ->and($project->goal)->toBeNull()
                ->and($project->outcome)->toBeNull()
                ->and($project->features)->toBeNull()
                ->and($project->home_summary)->toBeNull();
        });

        it('are translatable per locale', function (): void {
            $project = Project::factory()->create();
            $project->setTranslation('goal', 'it', 'Obiettivo')->setTranslation('goal', 'en', 'Goal')->save();

            $project->refresh();

            expect(runWithLocale('it', fn () => $project->goal))->toBe('Obiettivo')
                ->and(runWithLocale('en', fn () => $project->goal))->toBe('Goal');
        });

        it('round-trips the features list per locale', function (): void {
            $it_features = [['title' => 'Ordini', 'description' => 'Gestione ordini'], ['title' => 'Report', 'description' => 'Report mensili']];
            $en_features = [['title' => 'Orders', 'description' => 'Order management']];
            $project = Project::factory()->create();
            $project->setTranslation('features', 'it', $it_features)->setTranslation('features', 'en', $en_features)->save();

            $project = Project::query()->findOrFail($project->id);

            expect(runWithLocale('it', fn () => $project->features))->toBe($it_features)
                ->and(runWithLocale('en', fn () => $project->features))->toBe($en_features);
        });
    });

    describe('kind_names', function (): void {
        it('joins the project kind tags with a plus', function (): void {
            $project = Project::factory()->create();
            $names = runWithLocale('en', function () use ($project): string {
                $project->attachTags(['Custom CRM', 'Web platform'], TagTypes::PROJECT_KIND->value);

                return $project->refresh()->load('tags')->kind_names;
            });

            expect($names)->toContain(' + ')
                ->and($names)->toContain('Custom CRM')
                ->and($names)->toContain('Web platform');
        });

        it('ignores tags of other types', function (): void {
            $project = Project::factory()->create();
            $names = runWithLocale('en', function () use ($project): string {
                $project->attachTag('Laravel', TagTypes::TAG->value);
                $project->attachTag('Vue', TagTypes::TECHNOLOGY->value);
                $project->attachTag('Custom CRM', TagTypes::PROJECT_KIND->value);

                return $project->refresh()->load('tags')->kind_names;
            });

            expect($names)->toBe('Custom CRM');
        });

        it('is an empty string without kind tags', function (): void {
            $project = Project::factory()->create();
            $project->attachTag('Laravel', TagTypes::TAG->value);

            expect(runWithLocale('en', fn () => $project->refresh()->load('tags')->kind_names))->toBeEmpty();
        });
    });
});

describe('accessors', function (): void {
    describe('seoVariables', function (): void {
        it('exposes the client name', function (): void {
            $project = Project::factory()->for(Client::factory()->create(['name' => 'Acme']))->create();
            $orphan = Project::factory()->withoutClient()->create();

            $variables = fn (Project $project): array => (fn (): array => $this->seoVariables())->call($project->load('tags'));

            expect($variables($project)['client'])->toBe('Acme')
                ->and($variables($orphan)['client'])->toBeEmpty();
        });
    });
});

describe('scopes', function (): void {
    describe('wherePublished', function (): void {
        it('only returns published projects', function (): void {
            $published = Project::factory()->published()->create();
            $unpublished = Project::factory()->unpublished()->create();

            $result = Project::query()->wherePublished()->get();

            expect(Project::query()->count())->toBe(2)
                ->and($result->contains($published))->toBeTrue()
                ->and($result->contains($unpublished))->toBeFalse();
        });
    });
});

describe('relationships', function (): void {
    describe('client', function (): void {
        it('belongs to a client', function (): void {
            $client = Client::factory()->create();
            $project = Project::factory()->for($client)->create();

            expect($project->load('client')->client)->toBe($client);
        });

        it('can have no client', function (): void {
            $project = Project::factory()->withoutClient()->create();

            expect($project->client_id)->toBeNull()
                ->and($project->load('client')->client)->toBeNull();
        });
    });
});

describe('factory', function (): void {
    it('fills every story field with the withStory state', function (): void {
        $project = Project::factory()->withStory()->create()->refresh();

        expect($project->getTranslation('starting_point', 'en'))->not->toBeEmpty()
            ->and($project->getTranslation('goal', 'it'))->not->toBeEmpty()
            ->and($project->getTranslation('outcome', 'en'))->not->toBeEmpty()
            ->and($project->getTranslation('home_summary', 'en'))->not->toBeEmpty()
            ->and($project->getTranslation('features', 'en'))->toBeArray()->not->toBeEmpty();
    });

    it('tags the project with kinds through the withKind state', function (): void {
        $project = Project::factory()->withKind(['Custom CRM'])->create()->load('tags');

        expect($project->kind_names)->toBe('Custom CRM');
    });

    it('removes the client with the withoutClient state', function (): void {
        expect(Project::factory()->withoutClient()->create()->client_id)->toBeNull();
    });
});
