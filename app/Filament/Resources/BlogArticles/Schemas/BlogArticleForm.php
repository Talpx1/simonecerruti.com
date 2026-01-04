<?php

declare(strict_types=1);

namespace App\Filament\Resources\BlogArticles\Schemas;

use App\Enums\BlogArticleStatuses;
use App\Models\BlogArticle;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Contracts\HasDescription;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class BlogArticleForm {
    public static function configure(Schema $schema): Schema {
        return $schema
            ->components([
                Checkbox::make('featured')
                    ->label(__('Featured'))
                    ->belowContent(__('It is shown at the top of the blog and in lists'))
                    ->columnSpanFull(),

                TextInput::make('title')
                    ->label(__('Title'))
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
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, ?string $state) {
                        if ($state) {
                            $set('slug', Str::slug($state));
                        }
                    })
                    ->partiallyRenderAfterStateUpdated()
                    ->belowContent(__('It is recommended not to change the slug after its creation.')),

                RichEditor::make('content')
                    ->label(__('Content'))
                    ->required()
                    ->columnSpanFull(),

                Textarea::make('summary')
                    ->label(__('Summary'))
                    ->string()
                    ->required(),

                FileUpload::make('featured_image_path')
                    ->disabled(fn (?BlogArticle $record, Get $get) => ! $record && ! $get('slug'))
                    ->label(__('Featured image'))
                    ->disk(config()->string('blog_article.featured_image.disk'))
                    ->visibility(config()->string('blog_article.featured_image.visibility'))
                    ->directory(fn (Get $get, ?BlogArticle $record) => Str::replacePlaceholders(config()->string('blog_article.featured_image.path'), ['slug' => $record->slug ?? $get('slug')]))
                    ->getUploadedFileNameForStorageUsing(fn (TemporaryUploadedFile $file, Get $get, ?BlogArticle $record) => Str::replacePlaceholders(
                        config()->string('blog_article.featured_image.file_name'), [
                            'extension' => $file->extension(),
                            'slug' => $record->slug ?? $get('slug'),
                        ]
                    ))
                    ->image()
                    ->maxSize(config()->integer('blog_article.featured_image.max_file_size_kb'))
                    ->imageEditor()
                    ->imageEditorAspectRatios([null, '16:9', '4:3', '1:1'])
                    ->downloadable()
                    ->openable()
                    ->required(),

                Select::make('status')
                    ->label(__('Status'))
                    ->live()
                    ->partiallyRenderAfterStateUpdated()
                    ->options(BlogArticleStatuses::class)
                    ->enum(BlogArticleStatuses::class)
                    ->belowContent(fn ((BlogArticleStatuses&HasDescription)|null $state) => $state?->getDescription())
                    ->required(),

                DateTimePicker::make('published_at')
                    ->label(__('Published at'))
                    ->native(false)
                    ->displayFormat('l d/m/Y H:i:s'),

            ]);
    }
}
