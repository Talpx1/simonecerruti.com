<?php

declare(strict_types=1);

namespace App\Filament\Resources\Clients\Schemas;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
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

            SpatieMediaLibraryFileUpload::make('client_image')
                ->label('Immagine')
                ->collection('client_image')
                ->conversion('client_image_webp')
                ->image()
                ->imageEditor()
                ->automaticallyResizeImagesMode(config()->string('client.image.resize_mode'))
                ->imageAspectRatio(config()->string('client.image.aspect_ratio'))
                ->automaticallyResizeImagesToWidth((string) config()->integer('client.image.final_size_px'))
                ->automaticallyResizeImagesToHeight((string) config()->integer('client.image.final_size_px'))
                ->automaticallyCropImagesToAspectRatio()
                ->maxSize(config()->integer('client.image.max_file_size_kb')),
        ];
    }
}
