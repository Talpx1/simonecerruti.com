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
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Enums\Operation;
use Illuminate\Support\Str;

class BlogArticleForm {
    public static function configure(Schema $schema): Schema {
        return $schema
            ->components([
                FileUpload::make('featured_image_path')
                    ->columnSpanFull()
                    ->disabledOn('create')
                    ->label(__('Featured image'))
                    ->disk(config()->string('blog_article.featured_image.disk'))
                    ->visibility(config()->string('blog_article.featured_image.visibility'))
                    ->directory(fn (BlogArticle $record) => Str::replacePlaceholders(config()->string('blog_article.featured_image.path'), $record))
                    ->image()
                    ->maxSize(config()->integer('blog_article.featured_image.max_file_size_kb'))
                    ->imageEditor()
                    ->imageEditorViewportWidth(config()->integer('blog_article.featured_image.final_width_px'))
                    ->imageEditorViewportHeight(config()->integer('blog_article.featured_image.final_height_px'))
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio(config()->string('blog_article.featured_image.aspect_rateo'))
                    ->imageResizeTargetWidth((string) config()->integer('blog_article.featured_image.final_width_px'))
                    ->imageResizeTargetHeight((string) config()->integer('blog_article.featured_image.final_height_px'))
                    ->imageEditorAspectRatios([config()->string('blog_article.featured_image.aspect_rateo')])
                    ->downloadable()
                    ->openable()
                    ->required(fn (string $operation) => $operation === Operation::Edit->value)
                    ->belowLabel(fn (string $operation) => array_filter([
                        $operation === Operation::Create->value ? __('Available after saving') : null,
                        $operation === Operation::Edit->value
                        ? __('This image will be cropped to :dimensions', [
                            'dimensions' => config()->integer('blog_article.featured_image.final_width_px').'x'.config()->integer('blog_article.featured_image.final_height_px'),
                        ])
                        : null,
                    ])),

                Checkbox::make('featured')
                    ->label(__('Featured'))
                    ->belowContent(__('It is shown at the top of the blog and in lists'))
                    ->columnSpanFull(),

                TextInput::make('title')
                    ->label(__('Title'))
                    ->unique('blog_articles', "title->{$schema->getLivewire()->activeLocale}")
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
                    ->unique('blog_articles', "slug->{$schema->getLivewire()->activeLocale}")
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
                    ->columnSpanFull()
                    ->toolbarButtons([
                        ['bold', 'italic', 'underline', 'strike', 'highlight', 'subscript', 'superscript', 'link', 'textColor'],
                        ['h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd'],
                        ['blockquote', 'bulletList', 'orderedList'],
                        ['table', 'grid', 'gridDelete'/* 'attachFiles' */],
                        ['clearFormatting', 'undo', 'redo'],
                    ]),

                Textarea::make('summary')
                    ->label(__('Summary'))
                    ->string()
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
                    ->displayFormat('l d/m/Y H:i:s')
                    ->belowContent(__('If the status is set to published, but the published date is in the future, the article will be displayed only starting from that date.')),

                SpatieTagsInput::make('tags'),
            ]);
    }
}
