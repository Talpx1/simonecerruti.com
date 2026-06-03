<?php

declare(strict_types=1);

namespace App\Filament\Resources\VisitSessions\Tables;

use App\Enums\DeviceType;
use App\Enums\TagTypes;
use App\Enums\VisitMediumType;
use App\Enums\VisitSourceType;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Tags\Tag;

class VisitSessionsTable {
    public static function configure(Table $table): Table {
        return $table
            ->columns([
                TextColumn::make('started_at')
                    ->label(__('Started at'))
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),

                TextColumn::make('source')
                    ->label(__('Source'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => VisitSourceType::tryFrom($state)?->getLabel() ?? $state)
                    ->color(fn (string $state) => VisitSourceType::tryFrom($state)?->getColor() ?? 'gray')
                    ->sortable(),

                TextColumn::make('medium')
                    ->label(__('Medium'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => VisitMediumType::tryFrom($state)?->getLabel() ?? $state)
                    ->color(fn (string $state) => VisitMediumType::tryFrom($state)?->getColor() ?? 'gray')
                    ->toggleable(),

                TextColumn::make('campaign.name')
                    ->label(__('Campaign'))
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('utm_campaign')
                    ->label('utm_campaign')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('referrer_host')
                    ->label(__('Referrer'))
                    ->toggleable(),

                TextColumn::make('landing_path')
                    ->label(__('Landing'))
                    ->limit(40)
                    ->tooltip(fn ($record): ?string => $record?->landing_path)
                    ->toggleable(),

                TextColumn::make('pageview_count')
                    ->label(__('Pageviews'))
                    ->numeric()
                    ->sortable(),

                TextColumn::make('device_type')
                    ->label(__('Device'))
                    ->badge()
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('bot_score')
                    ->label(__('Bot score'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('country')
                    ->label(__('Country'))
                    ->badge()
                    ->toggleable(),

                TextColumn::make('locale')
                    ->label(__('Locale'))
                    ->badge()
                    ->toggleable(),
            ])
            ->filters([
                Filter::make('date_range')
                    ->schema([
                        DatePicker::make('from')->label(__('From')),
                        DatePicker::make('until')->label(__('Until')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn (Builder $q, $date) => $q->where('started_at', '>=', $date),
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $q, $date) => $q->where('started_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if (! empty($data['from'])) {
                            $indicators['from'] = __('From').' '.$data['from'];
                        }
                        if (! empty($data['until'])) {
                            $indicators['until'] = __('Until').' '.$data['until'];
                        }

                        return $indicators;
                    }),

                SelectFilter::make('source')
                    ->label(__('Source'))
                    ->options(VisitSourceType::class),

                SelectFilter::make('medium')
                    ->label(__('Medium'))
                    ->options(VisitMediumType::class),

                SelectFilter::make('campaign_id')
                    ->label(__('Campaign'))
                    ->relationship('campaign', 'name'),

                SelectFilter::make('campaign_tags')
                    ->label(__('Campaign tags'))
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

                        return $query->whereHas(
                            'campaign.tags',
                            fn (Builder $q) => $q->whereIn('tags.id', $data['values'])
                        );
                    }),

                SelectFilter::make('device_type')
                    ->label(__('Device'))
                    ->multiple()
                    ->options(DeviceType::class),

                SelectFilter::make('locale')
                    ->label(__('Locale'))
                    ->options(['it' => 'IT', 'en' => 'EN']),

                TernaryFilter::make('consent_analytics')
                    ->label(__('Analytics consent'))
                    ->boolean(),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([])
            ->defaultSort('started_at', 'desc');
    }
}
