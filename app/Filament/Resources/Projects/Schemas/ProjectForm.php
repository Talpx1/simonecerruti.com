<?php

declare(strict_types=1);

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProjectForm {
    public static function configure(Schema $schema): Schema {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informazioni Principali')
                    ->schema([
                        TextInput::make('title')
                            ->label('Titolo')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, ?string $state, Set $set) {
                                if ($operation === 'create' && filled($state)) {
                                    $set('slug', Str::slug($state));
                                }
                            })
                            ->columnSpanFull(),

                        TextInput::make('client')
                            ->label('Cliente')
                            ->maxLength(255),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique('projects', "title->{$schema->getLivewire()->activeLocale}")
                            ->maxLength(255)
                            ->helperText('Identificatore univoco per la URL'),
                    ])
                    ->columns(2),

                Section::make('Descrizioni')
                    ->schema([
                        Textarea::make('short_description')
                            ->label('Descrizione Breve')
                            ->rows(3)
                            ->maxLength(500),

                        RichEditor::make('description')
                            ->label('Descrizione Completa')
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('projects/attachments'),
                    ]),

                Section::make('Link')
                    ->schema([
                        TextInput::make('external_link')
                            ->label('Link Esterno al Progetto')
                            ->url()
                            ->placeholder('https://...'),

                        Repeater::make('links')
                            ->label('Link Aggiuntivi')
                            ->schema([
                                TextInput::make('url')
                                    ->label('URL')
                                    ->required()
                                    ->url()
                                    ->placeholder('https://...')
                                    ->maxLength(500),
                            ])
                            ->defaultItems(0)
                            ->addActionLabel('Aggiungi link')
                            ->reorderable()
                            ->collapsible()
                            ->columnSpanFull(),
                    ]),

                Section::make('Tecnologie & Tag')
                    ->schema([
                        SpatieTagsInput::make('technologies')
                            ->type('technologies')
                            ->label('Tecnologie')
                            ->splitKeys(['Tab', ','])
                            ->columnSpanFull(),

                        SpatieTagsInput::make('tags')
                            ->type('tags')
                            ->label('Tags')
                            ->columnSpanFull(),
                    ])
                    ->columns(1),

                Section::make('Immagini')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('featured_image')
                            ->label('Immagine in Evidenza')
                            ->collection('featured_image')
                            ->conversion('featured_image_webp')
                            ->image()
                            ->automaticallyResizeImagesMode(config()->string('project.featured_image.resize_mode'))
                            ->imageAspectRatio(config()->string('project.featured_image.aspect_ratio'))
                            ->automaticallyResizeImagesToWidth((string) config()->integer('project.featured_image.final_width_px'))
                            ->automaticallyResizeImagesToHeight((string) config()->integer('project.featured_image.final_height_px'))
                            ->automaticallyCropImagesToAspectRatio()
                            ->maxSize(config()->integer('project.featured_image.max_file_size_kb'))
                            ->columnSpanFull(),

                        SpatieMediaLibraryFileUpload::make('gallery')
                            ->label('Gallery')
                            ->collection('gallery')
                            ->conversion('gallery_webp')
                            ->multiple()
                            ->reorderable()
                            ->image()
                            ->automaticallyResizeImagesMode(config()->string('project.gallery.resize_mode'))
                            ->imageAspectRatio(config()->string('project.gallery.aspect_ratio'))
                            ->automaticallyCropImagesToAspectRatio()
                            ->automaticallyResizeImagesToWidth((string) config()->integer('project.gallery.final_width_px'))
                            ->automaticallyResizeImagesToHeight((string) config()->integer('project.gallery.final_height_px'))
                            ->maxSize(config()->integer('project.gallery.max_file_size_kb'))
                            ->maxFiles(config()->integer('project.gallery.max_files'))
                            ->columnSpanFull(),
                    ]),

                Section::make('Impostazioni')
                    ->schema([
                        Toggle::make('published')
                            ->label('Pubblicato')
                            ->default(false),

                        Toggle::make('featured')
                            ->label('In Evidenza')
                            ->default(false),
                    ])
                    ->columns(1),
            ]);
    }
}
