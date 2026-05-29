<?php

declare(strict_types=1);

namespace App\Filament\Resources\VisitSessions\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PageViewsRelationManager extends RelationManager {
    protected static string $relationship = 'pageViews';

    public function table(Table $table): Table {
        return $table
            ->recordTitleAttribute('url_path')
            ->columns([
                TextColumn::make('viewed_at')
                    ->label(__('Viewed at'))
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),

                TextColumn::make('url_path')
                    ->label(__('Path'))
                    ->searchable()
                    ->limit(60)
                    ->tooltip(fn ($record): ?string => $record?->url_path),

                TextColumn::make('route_name')
                    ->label(__('Route'))
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('locale')
                    ->label(__('Locale'))
                    ->badge(),
            ])
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([])
            ->defaultSort('viewed_at', 'desc');
    }
}
