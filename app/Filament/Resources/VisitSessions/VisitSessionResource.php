<?php

declare(strict_types=1);

namespace App\Filament\Resources\VisitSessions;

use App\Filament\Resources\Concerns\HasLocalizedLabels;
use App\Filament\Resources\VisitSessions\Pages\ListVisitSessions;
use App\Filament\Resources\VisitSessions\Pages\ViewVisitSession;
use App\Filament\Resources\VisitSessions\RelationManagers\PageViewsRelationManager;
use App\Filament\Resources\VisitSessions\Schemas\VisitSessionInfolist;
use App\Filament\Resources\VisitSessions\Tables\VisitSessionsTable;
use App\Models\VisitSession;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class VisitSessionResource extends Resource {
    use HasLocalizedLabels;

    protected static ?string $model = VisitSession::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $recordTitleAttribute = 'id';

    public static function infolist(Schema $schema): Schema {
        return VisitSessionInfolist::configure($schema);
    }

    public static function table(Table $table): Table {
        return VisitSessionsTable::configure($table);
    }

    public static function getRelations(): array {
        return [
            PageViewsRelationManager::class,
        ];
    }

    public static function getPages(): array {
        return [
            'index' => ListVisitSessions::route('/'),
            'view' => ViewVisitSession::route('/{record}'),
        ];
    }

    public static function canCreate(): bool {
        return false;
    }

    public static function canEdit(Model $record): bool {
        return false;
    }

    public static function canDelete(Model $record): bool {
        return false;
    }

    public static function canDeleteAny(): bool {
        return false;
    }
}
