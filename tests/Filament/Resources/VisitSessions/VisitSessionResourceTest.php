<?php

declare(strict_types=1);

use App\Enums\DeviceType;
use App\Enums\VisitMediumType;
use App\Enums\VisitSourceType;
use App\Filament\Resources\VisitSessions\Pages\ListVisitSessions;
use App\Filament\Resources\VisitSessions\Pages\ViewVisitSession;
use App\Filament\Resources\VisitSessions\VisitSessionResource;
use App\Models\Campaign;
use App\Models\VisitSession;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    authenticateAsAdmin();
});

describe('authorization', function (): void {
    it('forbids creating, editing, and deleting', function (): void {
        $session = VisitSession::factory()->create();

        expect(VisitSessionResource::canCreate())->toBeFalse()
            ->and(VisitSessionResource::canEdit($session))->toBeFalse()
            ->and(VisitSessionResource::canDelete($session))->toBeFalse()
            ->and(VisitSessionResource::canDeleteAny())->toBeFalse();
    });

    it('exposes only index and view pages', function (): void {
        expect(\array_keys(VisitSessionResource::getPages()))->toBe(['index', 'view']);
    });

    it('forbids users who cannot access the panel', function (): void {
        $session = VisitSession::factory()->create();

        assertPanelUrlIsForbiddenForNonAdminUser(VisitSessionResource::getUrl('index'));
        assertPanelUrlIsForbiddenForNonAdminUser(VisitSessionResource::getUrl('view', ['record' => $session]));
    });

    it('redirects guests to the login page', function (): void {
        $session = VisitSession::factory()->create();

        assertPanelUrlRedirectsGuestToLogin(VisitSessionResource::getUrl('index'));
        assertPanelUrlRedirectsGuestToLogin(VisitSessionResource::getUrl('view', ['record' => $session]));
    });
});

describe('list page', function (): void {
    it('lists sessions sorted by started_at desc', function (): void {
        $older = VisitSession::factory()->create(['started_at' => now()->subDays(2)]);
        $newer = VisitSession::factory()->create(['started_at' => now()]);

        livewire(ListVisitSessions::class)
            ->assertCanSeeTableRecords([$newer, $older], inOrder: true);
    });

    it('filters by source', function (): void {
        $direct = VisitSession::factory()->direct()->create();
        $social = VisitSession::factory()->fromSocial()->create();

        livewire(ListVisitSessions::class)
            ->filterTable('source', VisitSourceType::DIRECT->value)
            ->assertCanSeeTableRecords([$direct])
            ->assertCanNotSeeTableRecords([$social]);
    });

    it('filters by medium', function (): void {
        $social = VisitSession::factory()->fromSocial()->create();
        $direct = VisitSession::factory()->direct()->create();

        livewire(ListVisitSessions::class)
            ->filterTable('medium', VisitMediumType::SOCIAL->value)
            ->assertCanSeeTableRecords([$social])
            ->assertCanNotSeeTableRecords([$direct]);
    });

    it('filters by campaign relationship', function (): void {
        $campaign = Campaign::factory()->create();
        $matched = VisitSession::factory()->fromCampaign($campaign)->create();
        $other = VisitSession::factory()->direct()->create();

        livewire(ListVisitSessions::class)
            ->filterTable('campaign_id', $campaign->id)
            ->assertCanSeeTableRecords([$matched])
            ->assertCanNotSeeTableRecords([$other]);
    });

    it('filters by device type, excluding bots', function (): void {
        $human = VisitSession::factory()->create(['device_type' => DeviceType::DESKTOP]);
        $bot = VisitSession::factory()->create(['device_type' => DeviceType::BOT]);

        livewire(ListVisitSessions::class)
            ->filterTable('device_type', [DeviceType::MOBILE->value, DeviceType::TABLET->value, DeviceType::DESKTOP->value])
            ->assertCanSeeTableRecords([$human])
            ->assertCanNotSeeTableRecords([$bot]);
    });

    it('filters by date range', function (): void {
        $inside = VisitSession::factory()->create(['started_at' => now()->subDay()]);
        $outside = VisitSession::factory()->create(['started_at' => now()->subDays(10)]);

        livewire(ListVisitSessions::class)
            ->filterTable('date_range', [
                'from' => now()->subDays(3)->toDateString(),
                'until' => now()->toDateString(),
            ])
            ->assertCanSeeTableRecords([$inside])
            ->assertCanNotSeeTableRecords([$outside]);
    });
});

describe('view page', function (): void {
    it('renders the session details in the infolist', function (): void {
        $session = VisitSession::factory()->fromSocial('instagram')->create(['device_type' => DeviceType::DESKTOP]);

        livewire(ViewVisitSession::class, ['record' => $session->getKey()])
            ->assertSuccessful()
            ->assertSee($session->landing_path)
            ->assertSee('instagram.com')
            ->assertSee($session->country);
    });
});
