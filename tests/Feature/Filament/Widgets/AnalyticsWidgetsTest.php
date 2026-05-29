<?php

declare(strict_types=1);

use App\Filament\Widgets\AnalyticsStatsOverview;
use App\Filament\Widgets\TopCampaignsTable;
use App\Filament\Widgets\TopPagesTable;
use App\Filament\Widgets\TopReferrersTable;
use App\Filament\Widgets\VisitsByDayChart;
use App\Filament\Widgets\VisitsBySourcePieChart;
use App\Models\Campaign;
use App\Models\PageView;
use App\Models\VisitSession;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->withoutVite();
    actingAsAdmin();
});

describe('AnalyticsStatsOverview', function () {
    it('counts visits today, last 7 and 30 days', function () {
        VisitSession::factory()->count(2)->create(['started_at' => now()]);
        VisitSession::factory()->create(['started_at' => now()->subDays(3)]);
        VisitSession::factory()->create(['started_at' => now()->subDays(20)]);
        VisitSession::factory()->create(['started_at' => now()->subDays(60)]);

        livewire(AnalyticsStatsOverview::class)
            ->assertSee('2')
            ->assertSee('3')
            ->assertSee('4');
    });

    it('picks the top source over the last 30 days', function () {
        VisitSession::factory()->count(3)->fromSocial('instagram')->create(['started_at' => now()]);
        VisitSession::factory()->direct()->create(['started_at' => now()]);

        livewire(AnalyticsStatsOverview::class)
            ->assertSee('instagram');
    });
});

describe('VisitsByDayChart', function () {
    it('renders 30 buckets', function () {
        VisitSession::factory()->count(2)->create(['started_at' => now()]);

        $reflection = new ReflectionMethod(VisitsByDayChart::class, 'getData');
        $reflection->setAccessible(true);
        $data = $reflection->invoke(new VisitsByDayChart);

        expect($data['labels'])->toHaveCount(30)
            ->and($data['datasets'][0]['data'])->toHaveCount(30);
    });
});

describe('VisitsBySourcePieChart', function () {
    it('groups recent visits by source', function () {
        VisitSession::factory()->count(2)->fromSocial()->create(['started_at' => now()]);
        VisitSession::factory()->direct()->create(['started_at' => now()]);

        $reflection = new ReflectionMethod(VisitsBySourcePieChart::class, 'getData');
        $reflection->setAccessible(true);
        $data = $reflection->invoke(new VisitsBySourcePieChart);

        expect($data['labels'])->toHaveCount(2);
    });
});

describe('TopReferrersTable', function () {
    it('renders the widget', function () {
        $session = VisitSession::factory()->fromSocial('instagram')->create();
        VisitSession::factory()->direct()->create();

        livewire(TopReferrersTable::class)
            ->assertSuccessful()
            ->assertSee($session->referrer_host);
    });
});

describe('TopPagesTable', function () {
    it('renders the widget', function () {
        $session = VisitSession::factory()->create();
        PageView::factory()->count(2)->create([
            'visit_session_id' => $session->id,
            'url_path' => '/popular',
        ]);

        livewire(TopPagesTable::class)
            ->assertSuccessful()
            ->assertSee('/popular');
    });
});

describe('TopCampaignsTable', function () {
    it('renders the widget', function () {
        $campaign = Campaign::factory()->create(['name' => 'Hot Campaign']);
        VisitSession::factory()->count(3)->fromCampaign($campaign)->create();

        livewire(TopCampaignsTable::class)
            ->assertSuccessful()
            ->assertSee('Hot Campaign');
    });
});
