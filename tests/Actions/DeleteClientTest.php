<?php

declare(strict_types=1);

use App\Actions\DeleteClient;
use App\Models\Client;
use App\Models\Project;
use Illuminate\Support\Facades\Event;

describe('deletion', function (): void {
    it('deletes the client and nullifies client_id on its projects', function (): void {
        $client = Client::factory()->create();
        $projects = Project::factory()->count(2)->for($client)->create();
        $other_project = Project::factory()->for(Client::factory())->create();

        expect(Client::query()->find($client->id))->not->toBeNull()
            ->and($projects->every(fn (Project $project): bool => $project->refresh()->client_id === $client->id))->toBeTrue()
            ->and(DeleteClient::run($client))->toBeTrue()
            ->and(Client::query()->find($client->id))->toBeNull()
            ->and($projects->every(fn (Project $project): bool => $project->refresh()->client_id === null))->toBeTrue()
            ->and($other_project->refresh()->client_id)->not->toBeNull();
    });

    it('keeps the projects themselves', function (): void {
        $client = Client::factory()->create();
        Project::factory()->count(2)->for($client)->create();

        expect(Project::query()->count())->toBe(2);

        DeleteClient::run($client);

        expect(Project::query()->count())->toBe(2);
    });
});

describe('rollback', function (): void {
    it('keeps the project links when deleting the client fails', function (): void {
        $client = Client::factory()->create();
        $project = Project::factory()->for($client)->create();
        Event::listen('eloquent.deleting: '.Client::class, fn (): never => throw new RuntimeException('deletion blocked'));

        expect(fn (): bool => DeleteClient::run($client))->toThrow(RuntimeException::class, 'deletion blocked')
            ->and($project->refresh()->client_id)->toBe($client->id)
            ->and(Client::query()->find($client->id))->not->toBeNull();
    });
});
