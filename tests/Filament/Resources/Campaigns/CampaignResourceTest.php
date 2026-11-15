<?php

declare(strict_types=1);

use App\Filament\Resources\Campaigns\Pages\CreateCampaign;
use App\Filament\Resources\Campaigns\Pages\EditCampaign;
use App\Filament\Resources\Campaigns\Pages\ListCampaigns;
use App\Models\Campaign;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->withoutVite();
    actingAsAdmin();
});

it('lists campaigns', function () {
    $campaigns = Campaign::factory()->count(3)->create();

    livewire(ListCampaigns::class)
        ->assertCanSeeTableRecords($campaigns);
});

it('creates a campaign', function () {
    livewire(CreateCampaign::class)
        ->fillForm([
            'name' => 'Spring Launch',
            'slug' => 'spring-launch',
            'source' => 'instagram',
            'medium' => 'social',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Campaign::query()->where('slug', 'spring-launch')->exists())->toBeTrue();
});

it('auto-fills the slug from the name', function () {
    livewire(CreateCampaign::class)
        ->fillForm([
            'name' => 'Spring Launch 2026',
        ])
        ->assertFormSet([
            'slug' => 'spring-launch-2026',
        ]);
});

it('rejects a duplicate slug', function () {
    Campaign::factory()->create(['slug' => 'taken']);

    livewire(CreateCampaign::class)
        ->fillForm([
            'name' => 'Whatever',
            'slug' => 'taken',
            'source' => 'instagram',
        ])
        ->call('create')
        ->assertHasFormErrors(['slug']);
});

it('edits a campaign', function () {
    $campaign = Campaign::factory()->create(['name' => 'Old']);

    livewire(EditCampaign::class, ['record' => $campaign->id])
        ->fillForm(['name' => 'New'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($campaign->refresh()->name)->toBe('New');
});

it('searches campaigns by name', function () {
    $needle = Campaign::factory()->create(['name' => 'Findable']);
    $other = Campaign::factory()->create(['name' => 'Hidden']);

    livewire(ListCampaigns::class)
        ->searchTable('Findable')
        ->assertCanSeeTableRecords(collect([$needle]))
        ->assertCanNotSeeTableRecords(collect([$other]));
});

it('filters by active window', function () {
    $active = Campaign::factory()->create();
    $scheduled = Campaign::factory()->scheduled()->create();
    $ended = Campaign::factory()->ended()->create();

    livewire(ListCampaigns::class)
        ->filterTable('active_only')
        ->assertCanSeeTableRecords(collect([$active]))
        ->assertCanNotSeeTableRecords(collect([$scheduled, $ended]));
});

it('ends a campaign via End now action', function () {
    $campaign = Campaign::factory()->create();

    livewire(ListCampaigns::class)
        ->callTableAction('end_now', $campaign);

    expect($campaign->refresh()->ends_at)->not->toBeNull()
        ->and($campaign->refresh()->ends_at->isPast())->toBeTrue();
});
