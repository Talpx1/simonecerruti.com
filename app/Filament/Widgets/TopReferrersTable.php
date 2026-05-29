<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\VisitSourceType;
use App\Models\VisitSession;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class TopReferrersTable extends TableWidget {
    public function table(Table $table): Table {
        $inner = VisitSession::query()
            ->where('started_at', '>=', now()->subDays(30))
            ->whereNotIn('source', [VisitSourceType::INTERNAL->value, VisitSourceType::DIRECT->value])
            ->whereNotNull('referrer_host')
            ->selectRaw('MIN(CAST(id AS TEXT)) as id, referrer_host, source, COUNT(*) as total')
            ->groupBy('referrer_host', 'source');

        return $table
            ->heading(__('Top referrers (30 days)'))
            ->query(
                VisitSession::query()
                    ->fromSub($inner, 'visit_sessions')
                    ->orderByDesc('total')
                    ->limit(10),
            )
            ->paginated(false)
            ->columns([
                TextColumn::make('referrer_host')->label(__('Referrer host')),
                TextColumn::make('source')->label(__('Source'))->badge(),
                TextColumn::make('total')->label(__('Visits'))->numeric(),
            ]);
    }
}
