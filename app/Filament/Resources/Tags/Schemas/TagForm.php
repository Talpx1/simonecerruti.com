<?php

declare(strict_types=1);

namespace App\Filament\Resources\Tags\Schemas;

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
            ]);
    }
}
