<?php

declare(strict_types=1);

use App\Enums\VisitSourceType;
use App\Filament\Widgets\VisitsBySourcePieChart;
use App\Models\VisitSession;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    authenticateAsAdmin();
});

it('groups the visits of the last 30 days by source, most frequent first', function (): void {
    VisitSession::factory()->count(2)->fromSocial()->create(['started_at' => now()]);
    VisitSession::factory()->direct()->create(['started_at' => now()]);
    VisitSession::factory()->count(4)->direct()->create(['started_at' => now()->subDays(60)]);

    $data = getRenderedChartWidgetData(livewire(VisitsBySourcePieChart::class));

    expect($data['labels'])->toBe(['instagram', VisitSourceType::DIRECT->getLabel()])
        ->and($data['datasets'][0]['data'])->toBe([2, 1]);
});

it('renders no slices when there are no visits', function (): void {
    $data = getRenderedChartWidgetData(livewire(VisitsBySourcePieChart::class));

    expect($data['labels'])->toBeEmpty()
        ->and($data['datasets'][0]['data'])->toBe([]);
});
