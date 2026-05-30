<?php

declare(strict_types=1);

use App\Enums\DeviceType;
use App\Filament\Resources\VisitSessions\Pages\ListVisitSessions;
use App\Filament\Resources\VisitSessions\Pages\ViewVisitSession;
use App\Filament\Resources\VisitSessions\VisitSessionResource;
use App\Models\Campaign;
use App\Models\PageView;
use App\Models\VisitSession;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->withoutVite();
    actingAsAdmin();
});

describe('authorization', function () {
    it('forbids creating, editing, and deleting', function () {
        $session = VisitSession::factory()->create();

        expect(VisitSessionResource::canCreate())->toBeFalse()
            ->and(VisitSessionResource::canEdit($session))->toBeFalse()
            ->and(VisitSessionResource::canDelete($session))->toBeFalse()
            ->and(VisitSessionResource::canDeleteAny())->toBeFalse();
    });

    it('exposes only index and view pages', function () {
        $pages = array_keys(VisitSessionResource::getPages());

        expect($pages)->toMatchArray(['index', 'view']);
    });
});

describe('list page', function () {
    it('lists sessions sorted by started_at desc', function () {
        $older = VisitSession::factory()->create(['started_at' => now()->subDays(2)]);
        $newer = VisitSession::factory()->create(['started_at' => now()]);

        livewire(ListVisitSessions::class)
            ->assertCanSeeTableRecords(collect([$newer, $older]), inOrder: true);
    });

    it('filters by source (fallback bucket)', function () {
        $direct = VisitSession::factory()->direct()->create();
        $social = VisitSession::factory()->fromSocial()->create();

        livewire(ListVisitSessions::class)
            ->filterTable('source', 'direct')
            ->assertCanSeeTableRecords(collect([$direct]))
            ->assertCanNotSeeTableRecords(collect([$social]));
    });

    it('filters by medium', function () {
        $social = VisitSession::factory()->fromSocial()->create();
        $direct = VisitSession::factory()->direct()->create();

        livewire(ListVisitSessions::class)
            ->filterTable('medium', 'social')
            ->assertCanSeeTableRecords(collect([$social]))
            ->assertCanNotSeeTableRecords(collect([$direct]));
    });

    it('filters by campaign relationship', function () {
        $campaign = Campaign::factory()->create();
        $matched = VisitSession::factory()->fromCampaign($campaign)->create();
        $other = VisitSession::factory()->direct()->create();

        livewire(ListVisitSessions::class)
            ->filterTable('campaign_id', $campaign->id)
            ->assertCanSeeTableRecords(collect([$matched]))
            ->assertCanNotSeeTableRecords(collect([$other]));
    });

    it('filters by device type, excluding bots', function () {
        $human = VisitSession::factory()->create(['device_type' => DeviceType::DESKTOP->value]);
        $bot = VisitSession::factory()->create(['device_type' => DeviceType::BOT->value]);

        livewire(ListVisitSessions::class)
            ->filterTable('device_type', [DeviceType::MOBILE->value, DeviceType::TABLET->value, DeviceType::DESKTOP->value])
            ->assertCanSeeTableRecords(collect([$human]))
            ->assertCanNotSeeTableRecords(collect([$bot]));
    });

    it('filters by date range', function () {
        $inside = VisitSession::factory()->create(['started_at' => now()->subDay()]);
        $outside = VisitSession::factory()->create(['started_at' => now()->subDays(10)]);

        livewire(ListVisitSessions::class)
            ->filterTable('date_range', [
                'from' => now()->subDays(3)->toDateString(),
                'until' => now()->toDateString(),
            ])
            ->assertCanSeeTableRecords(collect([$inside]))
            ->assertCanNotSeeTableRecords(collect([$outside]));
    });
});

describe('view page', function () {
    it('renders the infolist for a session and its page views', function () {
        $session = VisitSession::factory()->create(['device_type' => DeviceType::DESKTOP->value]);
        PageView::factory()->count(2)->create(['visit_session_id' => $session->id]);

        livewire(ViewVisitSession::class, ['record' => $session->getKey()])
            ->assertSuccessful();
    });
});
