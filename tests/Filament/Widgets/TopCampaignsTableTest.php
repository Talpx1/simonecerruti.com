<?php

declare(strict_types=1);

use App\Filament\Widgets\TopCampaignsTable;
use App\Models\Campaign;
use App\Models\VisitSession;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    authenticateAsAdmin();
});

it('lists the active campaigns', function (): void {
    $campaign = Campaign::factory()->create(['name' => 'Hot Campaign']);
    VisitSession::factory()->count(3)->fromCampaign($campaign)->create();

    livewire(TopCampaignsTable::class)
        ->assertSuccessful()
        ->assertCanSeeTableRecords([$campaign])
        ->assertSee('Hot Campaign');
});

it('lists no campaigns when none exists', function (): void {
    livewire(TopCampaignsTable::class)
        ->assertSuccessful()
        ->assertCountTableRecords(0);
});
