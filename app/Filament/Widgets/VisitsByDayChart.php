<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\VisitSession;
use Carbon\CarbonImmutable;
use Filament\Widgets\ChartWidget;

class VisitsByDayChart extends ChartWidget {
    protected ?string $heading = 'Visite negli ultimi 30 giorni';

    protected ?string $pollingInterval = '60s';

    protected int|string|array $columnSpan = 'full';

    protected function getType(): string {
        return 'line';
    }

    protected function getData(): array {
        $start = CarbonImmutable::now()->subDays(29)->startOfDay();

        $rows = VisitSession::query()
            ->where('started_at', '>=', $start)
            ->selectRaw('DATE(started_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day');

        $labels = [];
        $values = [];

        for ($i = 0; $i < 30; $i++) {
            $day = $start->addDays($i);
            $key = $day->toDateString();
            $labels[] = $day->format('d/m');
            $values[] = (int) ($rows[$key] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => __('Visits'),
                    'data' => $values,
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
