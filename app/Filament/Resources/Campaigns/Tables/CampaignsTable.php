<?php

declare(strict_types=1);

namespace App\Filament\Resources\Campaigns\Tables;

use App\Enums\TagTypes;
use App\Enums\VisitMediumType;
use App\Models\Campaign;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Tags\Tag;

class CampaignsTable {
    public static function configure(Table $table): Table {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->withCount('visitSessions'))
            ->columns([
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->label(__('Slug'))
                    ->badge()
                    ->color('gray')
                    ->copyable()
                    ->searchable(),

                TextColumn::make('source')
                    ->label(__('Source'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('medium')
                    ->label(__('Medium'))
                    ->badge()
                    ->toggleable(),

                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->state(function (Campaign $record): string {
                        $now = now();
                        if ($record->starts_at !== null && $record->starts_at->isAfter($now)) {
                            return 'scheduled';
                        }
                        if ($record->ends_at !== null && $record->ends_at->isBefore($now)) {
                            return 'ended';
                        }

                        return 'active';
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'scheduled' => 'warning',
                        'ended' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => __(ucfirst($state))),

                TextColumn::make('starts_at')
                    ->label(__('Starts at'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('ends_at')
                    ->label(__('Ends at'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('visit_sessions_count')
                    ->label(__('Visits'))
                    ->numeric()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('Created at'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('source')
                    ->label(__('Source'))
                    ->options(fn (): array => Campaign::query()
                        ->select('source')
                        ->distinct()
                        ->orderBy('source')
                        ->pluck('source', 'source')
                        ->all()),

                SelectFilter::make('medium')
                    ->label(__('Medium'))
                    ->options(VisitMediumType::class),

                SelectFilter::make('tags')
                    ->label(__('Tags'))
                    ->multiple()
                    ->options(fn (): array => Tag::query()
                        ->withType(TagTypes::CAMPAIGN_TAG->value)
                        ->ordered()
                        ->pluck('name', 'id')
                        ->all())
                    ->query(function (Builder $query, array $data): Builder {
                        /** @var array{values: list<int|string>} $data */
                        if ($data['values'] === []) {
                            return $query;
                        }

                        return $query->whereHas('tags', fn (Builder $q) => $q->whereIn('tags.id', $data['values']));
                    }),

                Filter::make('active_only')
                    ->label(__('Active only'))
                    ->query(fn (Builder $query) => $query->active()),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('end_now')
                    ->label(__('End now'))
                    ->icon(Heroicon::OutlinedStopCircle)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Campaign $record): bool => $record->ends_at === null || $record->ends_at->isFuture())
                    ->action(fn (Campaign $record) => $record->update(['ends_at' => now()])),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
