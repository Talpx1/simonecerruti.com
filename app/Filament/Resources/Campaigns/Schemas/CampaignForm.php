<?php

declare(strict_types=1);

namespace App\Filament\Resources\Campaigns\Schemas;

use App\Enums\TagTypes;
use App\Enums\VisitMediumType;
use App\Models\Campaign;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Uri;
use Livewire\Component as LivewireComponent;

class CampaignForm {
    public static function configure(Schema $schema): Schema {
        return $schema
            ->components([
                Section::make(__('Outbound URL'))
                    ->description(__('Use this URL on the publishing channel. Each click is bound to this campaign.'))
                    ->hidden(fn (LivewireComponent $livewire): bool => $livewire instanceof CreateRecord)
                    ->schema([
                        TextEntry::make('outbound_url')
                            ->hiddenLabel()
                            ->copyable()
                            ->state(function (?Campaign $record): ?string {
                                if ($record === null) {
                                    return null;
                                }

                                return (string) Uri::of(config()->string('app.url'))->withQuery([
                                    'utm_source' => $record->source,
                                    'utm_medium' => $record->medium?->value,
                                    'utm_campaign' => $record->slug,
                                ]);
                            }),
                    ])
                    ->columnSpanFull(),

                TextInput::make('name')
                    ->label(__('Name'))
                    ->required()
                    ->string()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                        if (! $get('slug') && $state) {
                            $set('slug', Str::slug($state));
                        }
                    }),

                TextInput::make('slug')
                    ->label(__('Slug'))
                    ->required()
                    ->string()
                    ->unique('campaigns', 'slug', ignoreRecord: true)
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, ?string $state) {
                        if ($state) {
                            $set('slug', Str::slug($state));
                        }
                    })
                    ->belowContent(__('The slug is what matches ?utm_campaign=... in incoming URLs.')),

                TextInput::make('source')
                    ->label(__('Source'))
                    ->required()
                    ->string()
                    ->maxLength(255)
                    ->datalist(fn (): array => Campaign::query()
                        ->whereNotNull('source')
                        ->distinct()
                        ->orderBy('source')
                        ->pluck('source')
                        ->all())
                    ->belowContent(__('Examples: instagram, newsletter, qr, linkedin.')),

                Select::make('medium')
                    ->label(__('Medium'))
                    ->options(VisitMediumType::class)
                    ->native(false)
                    ->searchable(),

                DateTimePicker::make('starts_at')
                    ->label(__('Starts at'))
                    ->seconds(false)
                    ->belowContent(__('Leave empty to start immediately.')),

                DateTimePicker::make('ends_at')
                    ->label(__('Ends at'))
                    ->seconds(false)
                    ->belowContent(__('Leave empty for no expiry. Set to a past datetime to deactivate immediately.')),

                Textarea::make('description')
                    ->label(__('Description'))
                    ->columnSpanFull(),

                SpatieTagsInput::make('tags')
                    ->type(TagTypes::CAMPAIGN_TAG->value)
                    ->label(__('Tags'))
                    ->splitKeys(['Tab', ','])
                    ->columnSpanFull()
                    ->belowContent(__('Group campaigns across batches or materials. Example: material-business-card, batch-2026-spring.')),

                Textarea::make('notes')
                    ->label(__('Notes'))
                    ->columnSpanFull(),
            ]);
    }
}
