<?php

declare(strict_types=1);

use App\Models\Client;
use App\Models\Project;
use Spatie\Activitylog\Models\Activity;

describe('attributes', function () {
    describe('description', function () {
        it('is translatable per locale', function () {
            $client = Client::factory()->create(['description' => ['it' => 'Ciao', 'en' => 'Hello']]);

            expect($client->getTranslation('description', 'it'))->toBe('Ciao')
                ->and($client->getTranslation('description', 'en'))->toBe('Hello')
                ->and(withLocale('it', fn () => $client->description))->toBe('Ciao')
                ->and(withLocale('en', fn () => $client->description))->toBe('Hello');
        });

        it('reads as empty when not set', function () {
            expect(Client::factory()->withoutDescription()->create()->description)->toBeEmpty();
        });
    });
});

describe('relations', function () {
    it('has many projects', function () {
        $client = Client::factory()->create();
        $project = Project::factory()->for($client)->create();
        Project::factory()->withoutClient()->create();

        $client->load('projects');

        expect($client->projects)->toHaveCount(1)
            ->and($client->projects->first())->toBe($project);
    });

    it('is exposed on the project as client', function () {
        $client = Client::factory()->create();
        $project = Project::factory()->for($client)->create();

        expect($project->load('client')->client)->toBe($client);
    });
});

describe('mass-assignment', function () {
    it('fills name and description', function () {
        $client = Client::query()->create(['name' => 'Acme', 'description' => ['en' => 'Desc']]);

        expect($client->name)->toBe('Acme')
            ->and($client->getTranslation('description', 'en'))->toBe('Desc');
    });
});

describe('activity logging', function () {
    it('logs creation and dirty updates only', function () {
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

describe('factory', function () {
    it('creates a client with a translatable description', function () {
        $client = Client::factory()->create();

        expect($client->name)->not->toBeEmpty()
            ->and($client->getTranslation('description', 'it'))->not->toBeEmpty()
            ->and($client->getTranslation('description', 'en'))->not->toBeEmpty();
    });

    it('has a state without description', function () {
        $client = Client::factory()->withoutDescription()->create();

        expect($client->getTranslation('description', 'it'))->toBeEmpty()
            ->and($client->getTranslation('description', 'en'))->toBeEmpty();
    });
});
