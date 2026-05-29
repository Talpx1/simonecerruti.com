<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\VisitSourceType;
use App\Models\VisitSession;
use Filament\Widgets\ChartWidget;

class VisitsBySourcePieChart extends ChartWidget {
    protected ?string $heading = 'Sorgenti (ultimi 30 giorni)';

    protected ?string $pollingInterval = '60s';

    protected function getType(): string {
        return 'doughnut';
    }

    protected function getData(): array {
        $rows = VisitSession::query()
            ->where('started_at', '>=', now()->subDays(30))
            ->selectRaw('source, COUNT(*) as total')
            ->groupBy('source')
            ->orderByDesc('total')
            ->pluck('total', 'source');

        $labels = $rows->keys()
            ->map(fn (string $source): string => VisitSourceType::tryFrom($source)?->getLabel() ?? $source)
            ->all();

        return [
            'datasets' => [
                [
                    'label' => __('Visits'),
                    'data' => $rows->values()->all(),
                ],
            ],
            'labels' => $labels,
        ];
    }
}
