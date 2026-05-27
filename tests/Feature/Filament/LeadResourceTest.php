<?php

declare(strict_types=1);

use App\Filament\Resources\Leads\LeadResource;
use App\Filament\Resources\Leads\Pages\ListLeads;
use App\Models\Lead;
use Filament\Actions\Testing\TestAction;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->withoutVite();
    actingAsAdmin();
});

it('only registers an index page', function () {
    expect(array_keys(LeadResource::getPages()))->toBe(['index']);
});

it('lists leads', function () {
    $leads = Lead::factory()->count(3)->create();

    livewire(ListLeads::class)
        ->assertCanSeeTableRecords($leads);
});

it('marks a lead as read when it is viewed', function () {
    $lead = Lead::factory()->create();

    expect($lead->read_at)->toBeNull();

    livewire(ListLeads::class)
        ->callAction(TestAction::make('view')->table($lead));

    expect($lead->refresh()->read_at)->not->toBeNull();
});
