<?php

declare(strict_types=1);

use App\Filament\Resources\Leads\LeadResource;
use App\Filament\Resources\Leads\Pages\ListLeads;
use App\Models\Lead;
use Filament\Actions\Testing\TestAction;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    authenticateAsAdmin();
});

it('only registers an index page', function (): void {
    expect(\array_keys(LeadResource::getPages()))->toBe(['index']);
});

describe('access', function (): void {
    it('forbids users who cannot access the panel', function (): void {
        assertPanelUrlIsForbiddenForNonAdminUser(LeadResource::getUrl('index'));
    });

    it('redirects guests to the login page', function (): void {
        assertPanelUrlRedirectsGuestToLogin(LeadResource::getUrl('index'));
    });
});

describe('list page', function (): void {
    it('lists leads', function (): void {
        $leads = Lead::factory()->count(3)->create();

        livewire(ListLeads::class)
            ->assertCanSeeTableRecords($leads);
    });

    it('marks a lead as read when it is viewed', function (): void {
        $lead = Lead::factory()->create();

        expect($lead->read_at)->toBeNull();

        livewire(ListLeads::class)
            ->callAction(TestAction::make('view')->table($lead));

        expect($lead->refresh()->read_at)->not->toBeNull();
    });
});
