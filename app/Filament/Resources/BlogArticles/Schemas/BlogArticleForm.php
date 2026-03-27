<?php

declare(strict_types=1);

namespace App\Filament\Resources\BlogArticles\Schemas;

use App\Enums\BlogArticleStatuses;
use App\Enums\TagTypes;
use App\Models\Project;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Enums\Operation;
use Illuminate\Support\Str;

class BlogArticleForm {
    public static function configure(Schema $schema): Schema {
        $current_locale = $schema->getLivewire()->activeLocale;

        return $schema
            ->components([
                SpatieMediaLibraryFileUpload::make('featured_image')
                    ->columnSpanFull()
                    ->label(__('Featured image'))
                    ->collection('featured_image')
                    ->conversion('featured_image_webp')
                    ->image()
                    ->maxSize(config()->integer('blog_article.featured_image.max_file_size_kb'))
                    ->automaticallyResizeImagesMode(config()->string('blog_article.featured_image.resize_mode'))
                    ->automaticallyCropImagesToAspectRatio()
                    ->imageAspectRatio(config()->string('blog_article.featured_image.aspect_ratio'))
                    ->automaticallyResizeImagesToWidth((string) config()->integer('blog_article.featured_image.final_width_px'))
                    ->automaticallyResizeImagesToHeight((string) config()->integer('blog_article.featured_image.final_height_px'))
                    ->imageEditor()
                    ->imageEditorViewportWidth(config()->integer('blog_article.featured_image.final_width_px'))
                    ->imageEditorViewportHeight(config()->integer('blog_article.featured_image.final_height_px'))
                    ->imageEditorAspectRatioOptions([config()->string('blog_article.featured_image.aspect_ratio')])
                    ->downloadable()
                    ->openable()
                    ->belowLabel(fn (string $operation) => $operation === Operation::Edit->value
                        ? __('This image will be cropped to :dimensions', [
                            'dimensions' => config()->integer('blog_article.featured_image.final_width_px')
                                .'x'
                                .config()->integer('blog_article.featured_image.final_height_px'),
                        ])
                        : null
                    ),

                Toggle::make('featured')
                    ->label(__('Featured'))
                    ->belowContent(__('It is shown at the top of the blog and in lists'))
                    ->columnSpanFull(),

                TextInput::make('title')
                    ->label(__('Title'))
                    ->unique('blog_articles', "title->{$current_locale}")
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
                    ->unique('blog_articles', "slug->{$current_locale}")
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

                Section::make('Categorie & Tag')
                    ->schema([
                        SpatieTagsInput::make('categories')
                            ->type(TagTypes::BLOG_CATEGORY->value)
                            ->label('Categorie')
                            ->splitKeys(['Tab', ','])
                            ->columnSpanFull(),

                        SpatieTagsInput::make('tags')
                            ->type(TagTypes::TAG->value)
                            ->label('Tags')
                            ->columnSpanFull(),
                    ])
                    ->columns(1),

                Section::make(__('Related contents'))
                    ->schema([
                        Repeater::make('relatables')
                            ->relationship('relatables')
                            ->hiddenLabel()
                            ->addActionLabel('Aggiungi contenuto correlato')
                            ->schema([
                                MorphToSelect::make('relatable')
                                    ->label(__('Related content'))
                                    ->types([
                                        MorphToSelect\Type::make(Project::class)
                                            ->label(__('Project'))
                                            ->getOptionLabelFromRecordUsing(fn (Project $record): string => $record->title)
                                            ->titleAttribute('title'),
                                    ])
                                    ->searchable()
                                    ->required()
                                    ->columnSpanFull(),
                            ])
                            ->itemLabel(function (array $state): ?string {
                                $type = $state['relatable_type'] ?? null;
                                $id = $state['relatable_id'] ?? null;

                                if (! $type || ! $id) {
                                    return null;
                                }

                                return match ($type) {
                                    app(Project::class)->getMorphClass() => Project::find($id)?->title,
                                    default => null,
                                };
                            })
                            ->collapsed()
                            ->columnSpanFull(),
                    ])
                    ->columns(1),
            ]);
    }
}
