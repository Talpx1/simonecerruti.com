<?php

declare(strict_types=1);

use App\Models\Client;
use App\Models\Project;
use Illuminate\Http\UploadedFile;
use Spatie\Activitylog\Models\Activity;

describe('attributes', function (): void {
    describe('description', function (): void {
        it('is translatable per locale', function (): void {
            $client = Client::factory()->create(['description' => ['it' => 'Ciao', 'en' => 'Hello']]);

            expect($client->getTranslation('description', 'it'))->toBe('Ciao')
                ->and($client->getTranslation('description', 'en'))->toBe('Hello')
                ->and(runWithLocale('it', fn () => $client->description))->toBe('Ciao')
                ->and(runWithLocale('en', fn () => $client->description))->toBe('Hello');
        });

        it('reads as empty when not set', function (): void {
            expect(Client::factory()->withoutDescription()->create()->description)->toBeEmpty();
        });
    });

    describe('client_image', function (): void {
        it('registers a single file collection', function (): void {
            $collection = Client::factory()->make()->getRegisteredMediaCollections()->firstWhere('name', 'client_image');

            expect($collection->singleFile)->toBeTrue();
        });

        it('generates a square webp conversion', function (): void {
            $client = Client::factory()->create();
            $media = $client->addMedia(UploadedFile::fake()->image('logo.jpg', 1200, 800))->toMediaCollection('client_image');

            [$width, $height] = \getimagesize($media->refresh()->getPath('client_image_webp'));

            expect($width)->toBe(600)->and($height)->toBe(600);
        });

        it('replaces the previous image when a new one is attached', function (): void {
            $client = Client::factory()->create();
            $client->addMedia(UploadedFile::fake()->image('a.jpg', 800, 800))->toMediaCollection('client_image');

            expect($client->getMedia('client_image'))->toHaveCount(1)
                ->and($client->addMedia(UploadedFile::fake()->image('b.jpg', 800, 800))->toMediaCollection('client_image')->file_name)->toBe('b.jpg')
                ->and($client->refresh()->getMedia('client_image'))->toHaveCount(1)
                ->and($client->getFirstMedia('client_image')->file_name)->toBe('b.jpg');
        });
    });

    describe('image_url', function (): void {
        it('is null without media', function (): void {
            expect(Client::factory()->create()->image_url)->toBeNull();
        });

        it('returns the conversion url when an image is attached', function (): void {
            $client = Client::factory()->create();

            expect($client->image_url)->toBeNull()
                ->and($client->addMedia(UploadedFile::fake()->image('logo.jpg', 800, 800))->toMediaCollection('client_image'))->not->toBeNull()
                ->and($client->refresh()->image_url)->toContain('client_image_webp');
        });
    });
});

describe('relationships', function (): void {
    describe('projects', function (): void {
        it('has many projects', function (): void {
            $client = Client::factory()->create();
            $project = Project::factory()->for($client)->create();
            Project::factory()->withoutClient()->create();

            $client->load('projects');

            expect($client->projects)->toHaveCount(1)
                ->and($client->projects->first())->toBe($project);
        });
    });
});

describe('mass-assignment', function (): void {
    it('fills name and description', function (): void {
        $client = Client::query()->create(['name' => 'Acme', 'description' => ['en' => 'Desc']]);

        expect($client->name)->toBe('Acme')
            ->and($client->getTranslation('description', 'en'))->toBe('Desc');
    });
});

describe('activity logging', function (): void {
    it('logs creation and dirty updates only', function (): void {
        $client = Client::factory()->create(['name' => 'Before']);

        $count = fn (): int => Activity::query()
            ->where('subject_type', $client->getMorphClass())
            ->where('subject_id', $client->id)
            ->count();

        expect($count())->toBe(1);

        $client->update(['name' => 'After']);

        expect($count())->toBe(2)
            ->and(Activity::query()->latest('id')->first()->properties->get('attributes')['name'])->toBe('After');

        $client->update(['name' => 'After']);

        expect($count())->toBe(2);
    });
});

describe('factory', function (): void {
    it('creates a client with a translatable description', function (): void {
        $client = Client::factory()->create();

        expect($client->name)->not->toBeEmpty()
            ->and($client->getTranslation('description', 'it'))->not->toBeEmpty()
            ->and($client->getTranslation('description', 'en'))->not->toBeEmpty();
    });

    it('has a state without description', function (): void {
        $client = Client::factory()->withoutDescription()->create();

        expect($client->getTranslation('description', 'it'))->toBeEmpty()
            ->and($client->getTranslation('description', 'en'))->toBeEmpty();
    });
});
