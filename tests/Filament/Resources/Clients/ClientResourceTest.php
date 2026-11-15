<?php

declare(strict_types=1);

use App\Filament\Resources\Clients\ClientResource;
use App\Filament\Resources\Clients\Pages\CreateClient;
use App\Filament\Resources\Clients\Pages\EditClient;
use App\Filament\Resources\Clients\Pages\ListClients;
use App\Models\Client;
use App\Models\Project;
use Filament\Actions\DeleteAction;
use Filament\Actions\Testing\TestAction;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    authenticateAsAdmin();
});

describe('access', function (): void {
    it('forbids users who cannot access the panel', function (): void {
        $client = Client::factory()->create();

        assertPanelUrlIsForbiddenForNonAdminUser(ClientResource::getUrl('index'));
        assertPanelUrlIsForbiddenForNonAdminUser(ClientResource::getUrl('create'));
        assertPanelUrlIsForbiddenForNonAdminUser(ClientResource::getUrl('edit', ['record' => $client]));
    });

    it('redirects guests to the login page', function (): void {
        $client = Client::factory()->create();

        assertPanelUrlRedirectsGuestToLogin(ClientResource::getUrl('index'));
        assertPanelUrlRedirectsGuestToLogin(ClientResource::getUrl('create'));
        assertPanelUrlRedirectsGuestToLogin(ClientResource::getUrl('edit', ['record' => $client]));
    });
});

describe('list page', function (): void {
    it('lists clients', function (): void {
        $clients = Client::factory()->count(3)->create();

        livewire(ListClients::class)
            ->assertCanSeeTableRecords($clients);
    });

    it('detaches projects instead of deleting them when a client is deleted', function (): void {
        $client = Client::factory()->create();
        $project = Project::factory()->for($client)->create();

        expect($project->client_id)->toBe($client->id);

        livewire(ListClients::class)
            ->callAction(TestAction::make(DeleteAction::class)->table($client))
            ->assertNotified();

        expect(Client::query()->find($client->id))->toBeNull()
            ->and($project->refresh()->client_id)->toBeNull();
    });
});

describe('create page', function (): void {
    it('creates a client', function (): void {
        expect(Client::query()->count())->toBe(0);

        livewire(CreateClient::class)
            ->fillForm([
                'name' => 'Acme',
                'description' => 'Un cliente storico',
            ])
            ->call('create')
            ->assertNotified()
            ->assertHasNoFormErrors();

        expect(Client::query()->count())->toBe(1)
            ->and(Client::query()->firstOrFail()->name)->toBe('Acme');
    });

    it('requires a client name', function (): void {
        livewire(CreateClient::class)
            ->fillForm(['name' => null])
            ->call('create')
            ->assertHasFormErrors(['name' => 'required'])
            ->assertNotNotified();

        expect(Client::query()->count())->toBe(0);
    });
});

describe('edit page', function (): void {
    it('updates a client', function (): void {
        $client = Client::factory()->create(['name' => 'Original']);

        livewire(EditClient::class, ['record' => $client->id])
            ->fillForm(['name' => 'Renamed'])
            ->call('save')
            ->assertNotified()
            ->assertHasNoFormErrors();

        expect($client->refresh()->name)->toBe('Renamed');
    });

    it('detaches projects instead of deleting them when a client is deleted', function (): void {
        $client = Client::factory()->create();
        $project = Project::factory()->for($client)->create();

        expect($project->client_id)->toBe($client->id);

        livewire(EditClient::class, ['record' => $client->id])
            ->callAction(DeleteAction::class)
            ->assertNotified();

        expect(Client::query()->find($client->id))->toBeNull()
            ->and($project->refresh()->client_id)->toBeNull();
    });
});
