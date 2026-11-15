<?php

declare(strict_types=1);

use App\Filament\Widgets\VisitsByDayChart;
use App\Models\VisitSession;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    authenticateAsAdmin();
});

it('renders one bucket per day for the last 30 days with the visit counts', function (): void {
    VisitSession::factory()->count(2)->create(['started_at' => now()]);
    VisitSession::factory()->create(['started_at' => now()->subDays(3)]);
    VisitSession::factory()->create(['started_at' => now()->subDays(60)]);

    $data = getRenderedChartWidgetData(livewire(VisitsByDayChart::class));

    expect($data['labels'])->toHaveCount(30)
        ->and($data['labels'][0])->toBe(now()->subDays(29)->format('d/m'))
        ->and($data['labels'][29])->toBe(now()->format('d/m'))
        ->and($data['datasets'][0]['data'])->toHaveCount(30)
        ->and($data['datasets'][0]['data'][29])->toBe(2)
        ->and($data['datasets'][0]['data'][26])->toBe(1)
        ->and(\array_sum($data['datasets'][0]['data']))->toBe(3);
});

it('renders thirty empty buckets when there are no visits', function (): void {
    $data = getRenderedChartWidgetData(livewire(VisitsByDayChart::class));

    expect($data['labels'])->toHaveCount(30)
        ->and(\array_sum($data['datasets'][0]['data']))->toBe(0);
});
