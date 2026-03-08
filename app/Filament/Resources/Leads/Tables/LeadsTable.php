<?php

declare(strict_types=1);

namespace App\Filament\Resources\Leads\Tables;

use App\Models\Lead;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LeadsTable {
    public static function configure(Table $table): Table {
        return $table
            ->columns([
                TextColumn::make('read_status')
                    ->label(__('Status'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('source')
                    ->label(__('Source')),

                TextColumn::make('created_at')
                    ->label(__('Received at'))
                    ->sortable()
                    ->dateTime('l d/m/Y H:i:s'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()->mountUsing(function (Lead $record) {
                    if (is_null($record->read_at)) {
                        $record->update(['read_at' => now()]);
                    }
                }),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
