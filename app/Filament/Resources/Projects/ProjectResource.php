<?php

declare(strict_types=1);

namespace App\Filament\Resources\Projects;

use App\Filament\Resources\Concerns\HasLocalizedLabels;
use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Projects\Pages\EditProject;
use App\Filament\Resources\Projects\Pages\ListProjects;
use App\Filament\Resources\Projects\Schemas\ProjectForm;
use App\Filament\Resources\Projects\Tables\ProjectTable;
use App\Models\Project;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;

class ProjectResource extends Resource {
    use HasLocalizedLabels, Translatable;

    protected static ?string $model = Project::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFolderOpen;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema {
        return ProjectForm::configure($schema);
    }

    public static function table(Table $table): Table {
        return ProjectTable::configure($table);
    }

    public static function getRelations(): array {
        return [];
    }

    public static function getPages(): array {
        return [
            'index' => ListProjects::route('/'),
            'create' => CreateProject::route('/create'),
            'edit' => EditProject::route('/{record}/edit'),
        ];
    }
}
