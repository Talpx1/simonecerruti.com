<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Campaign;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class TopCampaignsTable extends TableWidget {
    public function table(Table $table): Table {
        return $table
            ->heading(__('Top campaigns (30 days)'))
            ->query(
                Campaign::query()
                    ->active()
                    ->withCount([
                        'visitSessions as recent_count' => fn (Builder $q) => $q->where('started_at', '>=', now()->subDays(30)),
                    ])
                    ->orderByDesc('recent_count')
                    ->limit(10),
            )
            ->paginated(false)
            ->columns([
                TextColumn::make('name')->label(__('Name')),
                TextColumn::make('source')->label(__('Source'))->badge(),
                TextColumn::make('medium')->label(__('Medium'))->placeholder('-'),
                TextColumn::make('recent_count')->label(__('Visits'))->numeric(),
            ]);
    }
}
