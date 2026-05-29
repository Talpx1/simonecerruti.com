<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\VisitSession;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AnalyticsStatsOverview extends StatsOverviewWidget {
    protected ?string $pollingInterval = '60s';

    protected function getStats(): array {
        $today = VisitSession::query()
            ->whereDate('started_at', now()->toDateString())
            ->count();

        $last7 = VisitSession::query()
            ->where('started_at', '>=', now()->subDays(7))
            ->count();

        $last30 = VisitSession::query()
            ->where('started_at', '>=', now()->subDays(30))
            ->count();

        $top_source = VisitSession::query()
            ->where('started_at', '>=', now()->subDays(30))
            ->selectRaw('source, COUNT(*) as total')
            ->groupBy('source')
            ->orderByDesc('total')
            ->first();

        return [
            Stat::make(__('Visits today'), (string) $today),
            Stat::make(__('Visits last 7 days'), (string) $last7),
            Stat::make(__('Visits last 30 days'), (string) $last30),
            Stat::make(
                __('Top source (30d)'),
                $top_source instanceof VisitSession ? (string) $top_source->source : '-',
            ),
        ];
    }
}
