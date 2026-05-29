<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\PageView;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class TopPagesTable extends TableWidget {
    public function table(Table $table): Table {
        $inner = PageView::query()
            ->where('viewed_at', '>=', now()->subDays(30))
            ->selectRaw('MIN(id) as id, url_path, route_name, COUNT(*) as total')
            ->groupBy('url_path', 'route_name');

        return $table
            ->heading(__('Top pages (30 days)'))
            ->query(
                PageView::query()
                    ->fromSub($inner, 'page_views')
                    ->orderByDesc('total')
                    ->limit(10),
            )
            ->paginated(false)
            ->columns([
                TextColumn::make('url_path')->label(__('Path'))->limit(60),
                TextColumn::make('route_name')->label(__('Route'))->placeholder('-'),
                TextColumn::make('total')->label(__('Visits'))->numeric(),
            ]);
    }
}
