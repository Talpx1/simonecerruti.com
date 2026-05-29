<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\DB;

class PanEventsWidget extends TableWidget {
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table {
        return $table
            ->heading(__('Pan events'))
            ->records(fn (): array => $this->getRecords())
            ->emptyStateHeading(__('No events tracked yet.'))
            ->paginated(false)
            ->columns([
                TextColumn::make('category')->label(__('Category'))->badge(),
                TextColumn::make('name')->label(__('Event'))->fontFamily('mono')->size('sm'),
                TextColumn::make('impressions')->label(__('Impressions'))->numeric()->alignEnd(),
                TextColumn::make('hovers')->label(__('Hovers'))->numeric()->alignEnd(),
                TextColumn::make('clicks')->label(__('Clicks'))->numeric()->alignEnd(),
            ]);
    }

    /**
     * @return array<int, array{category: string, name: string, impressions: int, hovers: int, clicks: int}>
     */
    protected function getRecords(): array {
        $rows = DB::table('pan_analytics')->orderBy('name')->get();

        $records = [];

        foreach ($rows as $row) {
            $name = is_string($row->name) ? $row->name : '';
            $id = is_numeric($row->id) ? (int) $row->id : 0;

            $records[$id] = [
                'category' => $this->categoryFor($name),
                'name' => $name,
                'impressions' => is_numeric($row->impressions) ? (int) $row->impressions : 0,
                'hovers' => is_numeric($row->hovers) ? (int) $row->hovers : 0,
                'clicks' => is_numeric($row->clicks) ? (int) $row->clicks : 0,
            ];
        }

        uasort($records, fn (array $a, array $b): int => [$a['category'], $a['name']] <=> [$b['category'], $b['name']]);

        return $records;
    }

    protected function categoryFor(string $name): string {
        return match (true) {
            str_starts_with($name, 'cta-nav-') => __('Navigation'),
            str_starts_with($name, 'cta-social-') => __('Social'),
            str_starts_with($name, 'cta-hero-') => __('Hero CTAs'),
            str_starts_with($name, 'cta-contact-') => __('Contact CTAs'),
            str_starts_with($name, 'card-') => __('Card clicks'),
            str_starts_with($name, 'section-impression-') => __('Section impressions'),
            default => __('Other'),
        };
    }
}
