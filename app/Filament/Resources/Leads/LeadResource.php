<?php

declare(strict_types=1);

namespace App\Filament\Resources\Leads;

use App\Filament\Resources\Leads\Pages\ListLeads;
use App\Filament\Resources\Leads\Schemas\LeadInfolist;
use App\Filament\Resources\Leads\Tables\LeadsTable;
use App\Models\Lead;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LeadResource extends Resource {
    protected static ?string $model = Lead::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    public static function infolist(Schema $schema): Schema {
        return LeadInfolist::configure($schema);
    }

    public static function table(Table $table): Table {
        return LeadsTable::configure($table);
    }

    public static function getRelations(): array {
        return [
            //
        ];
    }

    public static function getPages(): array {
        return [
            'index' => ListLeads::route('/'),
        ];
    }
}
