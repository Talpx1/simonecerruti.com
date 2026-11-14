<?php

declare(strict_types=1);

namespace App\Filament\Resources\Clients\Schemas;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ClientForm {
    public static function configure(Schema $schema): Schema {
        return $schema
            ->columns(1)
            ->components(self::getFields());
    }

    /**
     * @return list<Field>
     */
    public static function getFields(): array {
        return [
            TextInput::make('name')
                ->label('Nome')
                ->required()
                ->maxLength(255),

            Textarea::make('description')
                ->label('Descrizione')
                ->rows(4),
        ];
    }
}
