<?php

declare(strict_types=1);

use App\Enums\VisitSourceType;
use App\Filament\Widgets\AnalyticsStatsOverview;
use App\Models\VisitSession;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    authenticateAsAdmin();
});

it('counts visits today, in the last 7 days and in the last 30 days', function (): void {
    VisitSession::factory()->count(2)->create(['started_at' => now()]);
    VisitSession::factory()->create(['started_at' => now()->subDays(3)]);
    VisitSession::factory()->create(['started_at' => now()->subDays(20)]);
    VisitSession::factory()->create(['started_at' => now()->subDays(60)]);

    $stats = getRenderedStatsOverviewValues(livewire(AnalyticsStatsOverview::class));

    expect($stats[__('Visits today')])->toBe('2')
        ->and($stats[__('Visits last 7 days')])->toBe('3')
        ->and($stats[__('Visits last 30 days')])->toBe('4');
});

it('picks the top source over the last 30 days', function (): void {
    VisitSession::factory()->count(3)->fromSocial('instagram')->create(['started_at' => now()]);
    VisitSession::factory()->direct()->create(['started_at' => now()]);
    VisitSession::factory()->count(5)->direct()->create(['started_at' => now()->subDays(60)]);

    $stats = getRenderedStatsOverviewValues(livewire(AnalyticsStatsOverview::class));

    expect($stats[__('Top source (30d)')])->toBe('instagram')
        ->and($stats[__('Top source (30d)')])->not->toBe(VisitSourceType::DIRECT->value);
});

it('shows zero visits and a dash as top source when there are no visits', function (): void {
    $stats = getRenderedStatsOverviewValues(livewire(AnalyticsStatsOverview::class));

    expect($stats)->toBe([
        __('Visits today') => '0',
        __('Visits last 7 days') => '0',
        __('Visits last 30 days') => '0',
        __('Top source (30d)') => '-',
    ]);
});
