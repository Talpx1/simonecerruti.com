<?php

declare(strict_types=1);

namespace App\Filament\Resources\Leads\Schemas;

use App\Models\Lead;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;

class LeadInfolist {
    public static function configure(Schema $schema): Schema {
        return $schema
            ->components([
                TextEntry::make('source')->label(__('Source')),
                TextEntry::make('created_at')->label(__('Received at'))->dateTime('l d/m/Y H:i:s'),
                TextEntry::make('leadable.lead_title')->label(__('Title')),
                TextEntry::make('read_at')->label(__('First read at'))->dateTime('l d/m/Y H:i:s'),

                Fieldset::make('lead_details')
                    ->columnSpanFull()
                    ->label(fn (Lead $record) => $record->leadable->lead_title)
                    ->schema(fn (Lead $record) => collect($record->leadable->getInfolistComponents())
                        ->map(fn ($component) => $component->statePath('leadable.'.$component->getName()))
                        ->toArray()
                    ),
            ]);
    }
}
