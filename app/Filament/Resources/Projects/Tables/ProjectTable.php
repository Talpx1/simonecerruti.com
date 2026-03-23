<?php

declare(strict_types=1);

namespace App\Filament\Resources\BlogArticles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ProjectTable {
    public static function configure(Table $table): Table {
        return $table
            ->columns([
                ToggleColumn::make('featured')
                    ->label('In evidenza'),

                TextColumn::make('title')
                    ->label('Titolo')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                TextColumn::make('client')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),

                IconColumn::make('published')
                    ->label('Pubblicato')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Creato il')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Aggiornato il')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('published')->label('Pubblicato'),

            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
