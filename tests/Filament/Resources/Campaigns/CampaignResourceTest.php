<?php

declare(strict_types=1);

use App\Enums\VisitMediumType;
use App\Filament\Resources\Campaigns\CampaignResource;
use App\Filament\Resources\Campaigns\Pages\CreateCampaign;
use App\Filament\Resources\Campaigns\Pages\EditCampaign;
use App\Filament\Resources\Campaigns\Pages\ListCampaigns;
use App\Models\Campaign;
use Filament\Actions\Testing\TestAction;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    authenticateAsAdmin();
});

describe('access', function (): void {
    it('forbids users who cannot access the panel', function (): void {
        $campaign = Campaign::factory()->create();

        assertPanelUrlIsForbiddenForNonAdminUser(CampaignResource::getUrl('index'));
        assertPanelUrlIsForbiddenForNonAdminUser(CampaignResource::getUrl('create'));
        assertPanelUrlIsForbiddenForNonAdminUser(CampaignResource::getUrl('edit', ['record' => $campaign]));
    });

    it('redirects guests to the login page', function (): void {
        $campaign = Campaign::factory()->create();

        assertPanelUrlRedirectsGuestToLogin(CampaignResource::getUrl('index'));
        assertPanelUrlRedirectsGuestToLogin(CampaignResource::getUrl('create'));
        assertPanelUrlRedirectsGuestToLogin(CampaignResource::getUrl('edit', ['record' => $campaign]));
    });
});

describe('list page', function (): void {
    it('lists campaigns', function (): void {
        $campaigns = Campaign::factory()->count(3)->create();

        livewire(ListCampaigns::class)
            ->assertCanSeeTableRecords($campaigns);
    });

    it('searches campaigns by name', function (): void {
        $needle = Campaign::factory()->create(['name' => 'Findable']);
        $other = Campaign::factory()->create(['name' => 'Hidden']);

        livewire(ListCampaigns::class)
            ->searchTable('Findable')
            ->assertCanSeeTableRecords([$needle])
            ->assertCanNotSeeTableRecords([$other]);
    });

    it('filters by active window', function (): void {
        $active = Campaign::factory()->create();
        $scheduled = Campaign::factory()->scheduled()->create();
        $ended = Campaign::factory()->ended()->create();

        livewire(ListCampaigns::class)
            ->filterTable('active_only')
            ->assertCanSeeTableRecords([$active])
            ->assertCanNotSeeTableRecords([$scheduled, $ended]);
    });

    describe('end now action', function (): void {
        it('ends a campaign', function (): void {
            $campaign = Campaign::factory()->create();

            expect($campaign->ends_at)->toBeNull();

            livewire(ListCampaigns::class)
                ->callAction(TestAction::make('end_now')->table($campaign));

            expect($campaign->refresh()->ends_at)->not->toBeNull()
                ->and($campaign->ends_at->isPast())->toBeTrue();
        });

        it('is hidden for a campaign that already ended', function (): void {
            $ended = Campaign::factory()->ended()->create();

            livewire(ListCampaigns::class)
                ->assertActionHidden(TestAction::make('end_now')->table($ended));
        });
    });
});

describe('create page', function (): void {
    it('creates a campaign', function (): void {
        expect(Campaign::query()->where('slug', 'spring-launch')->exists())->toBeFalse();

        livewire(CreateCampaign::class)
            ->fillForm([
                'name' => 'Spring Launch',
                'slug' => 'spring-launch',
                'source' => 'instagram',
                'medium' => VisitMediumType::SOCIAL->value,
            ])
            ->call('create')
            ->assertNotified()
            ->assertHasNoFormErrors();

        $campaign = Campaign::query()->where('slug', 'spring-launch')->firstOrFail();

        expect($campaign->name)->toBe('Spring Launch')
            ->and($campaign->source)->toBe('instagram')
            ->and($campaign->medium)->toBe(VisitMediumType::SOCIAL);
    });

    it('auto-fills the slug from the name', function (): void {
        livewire(CreateCampaign::class)
            ->fillForm(['name' => 'Spring Launch 2026'])
            ->assertFormSet(['slug' => 'spring-launch-2026']);
    });

    it('validates the required fields', function (): void {
        livewire(CreateCampaign::class)
            ->fillForm([
                'name' => null,
                'slug' => null,
                'source' => null,
            ])
            ->call('create')
            ->assertHasFormErrors([
                'name' => 'required',
                'slug' => 'required',
                'source' => 'required',
            ])
            ->assertNotNotified();

        expect(Campaign::query()->count())->toBe(0);
    });

    it('rejects a duplicate slug', function (): void {
        Campaign::factory()->create(['slug' => 'taken']);

        livewire(CreateCampaign::class)
            ->fillForm([
                'name' => 'Whatever',
                'slug' => 'taken',
                'source' => 'instagram',
            ])
            ->call('create')
            ->assertHasFormErrors(['slug' => 'unique'])
            ->assertNotNotified();

        expect(Campaign::query()->count())->toBe(1);
    });
});

describe('edit page', function (): void {
    it('edits a campaign', function (): void {
        $campaign = Campaign::factory()->create(['name' => 'Old']);

        livewire(EditCampaign::class, ['record' => $campaign->id])
            ->fillForm(['name' => 'New'])
            ->call('save')
            ->assertNotified()
            ->assertHasNoFormErrors();

        expect($campaign->refresh()->name)->toBe('New');
    });
});
