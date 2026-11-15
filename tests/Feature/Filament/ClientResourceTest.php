<?php

declare(strict_types=1);

use App\Filament\Resources\Clients\Pages\CreateClient;
use App\Filament\Resources\Clients\Pages\EditClient;
use App\Filament\Resources\Clients\Pages\ListClients;
use App\Models\Client;
use App\Models\Project;
use Filament\Actions\DeleteAction;
use Filament\Actions\Testing\TestAction;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->withoutVite();
    actingAsAdmin();
});

it('lists clients', function () {
    $clients = Client::factory()->count(3)->create();

    livewire(ListClients::class)
        ->assertCanSeeTableRecords($clients);
});

it('creates a client', function () {
    livewire(CreateClient::class)
        ->fillForm([
            'name' => 'Acme',
            'description' => 'Un cliente storico',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Client::query()->firstOrFail()->name)->toBe('Acme');
});

it('requires a client name', function () {
    livewire(CreateClient::class)
        ->fillForm(['name' => null])
        ->call('create')
        ->assertHasFormErrors(['name' => 'required']);
});

it('updates a client', function () {
    $client = Client::factory()->create();

    livewire(EditClient::class, ['record' => $client->id])
        ->fillForm(['name' => 'Renamed'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($client->refresh()->name)->toBe('Renamed');
});

it('detaches projects instead of deleting them when a client is deleted from the table', function () {
    $client = Client::factory()->create();
    $project = Project::factory()->for($client)->create();

    livewire(ListClients::class)
        ->callAction(TestAction::make(DeleteAction::class)->table($client))
        ->assertNotified();

    expect(Client::query()->find($client->id))->toBeNull()
        ->and($project->refresh()->client_id)->toBeNull();
});

it('detaches projects instead of deleting them when a client is deleted from the edit page', function () {
    $client = Client::factory()->create();
    $project = Project::factory()->for($client)->create();

    livewire(EditClient::class, ['record' => $client->id])
        ->callAction(DeleteAction::class)
        ->assertNotified();

    expect(Client::query()->find($client->id))->toBeNull()
        ->and($project->refresh()->client_id)->toBeNull();
});
