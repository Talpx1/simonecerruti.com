<?php

declare(strict_types=1);

use App\Actions\DeleteClient;
use App\Models\Client;
use App\Models\Project;

it('deletes the client and nullifies client_id on its projects', function () {
    $client = Client::factory()->create();
    $projects = Project::factory()->count(2)->for($client)->create();
    $other_project = Project::factory()->for(Client::factory())->create();

    expect(DeleteClient::run($client))->toBeTrue()
        ->and(Client::query()->find($client->id))->toBeNull()
        ->and($projects->every(fn (Project $project): bool => $project->refresh()->client_id === null))->toBeTrue()
        ->and($other_project->refresh()->client_id)->not->toBeNull();
});
