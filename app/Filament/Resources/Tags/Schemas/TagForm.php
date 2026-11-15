<?php

declare(strict_types=1);

namespace App\Filament\Resources\Tags\Schemas;

use App\Enums\ServiceAreas;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class TagForm {
    public static function configure(Schema $schema): Schema {
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $operation, ?string $state, Set $set) {
                        if ($operation === 'create' && filled($state)) {
                            $set('slug', Str::slug($state));
                        }
                    }),

                TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->unique('tags', "slug->{$schema->getLivewire()->activeLocale}")
                    ->maxLength(255),

                Textarea::make('description')
                    ->label('Descrizione')
                    ->rows(3),

                Select::make('service')
                    ->label('Servizio')
                    ->options(ServiceAreas::class)
                    ->placeholder('Nessuno')
                    ->helperText('Collega il tag alla pagina di un servizio.'),
            ]);
    }
}
