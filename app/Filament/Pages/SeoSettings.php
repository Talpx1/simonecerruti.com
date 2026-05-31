<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Enums\SchemaType;
use App\Models\SeoSetting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\App;

/**
 * Site-wide SEO identity and defaults, edited on the single SeoSetting row.
 *
 * @property-read Schema $form
 */
class SeoSettings extends Page {
    protected string $view = 'filament.pages.seo-settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAlt;

    /** @var array<string, mixed> */
    public array $data = [];

    public function mount(): void {
        $record = $this->getRecord();

        $this->form->fill([
            'type' => $record->type->value,
            'name' => $record->name,
            'description' => $this->translationsFor($record, 'description'),
            'social_profiles' => $record->social_profiles ?? [],
            'default_og_image' => $record->default_og_image,
            'title_separator' => $record->title_separator,
            'website_schema' => $record->website_schema,
        ]);
    }

    public static function getNavigationLabel(): string {
        return __('SEO settings');
    }

    public function getTitle(): string {
        return __('SEO settings');
    }

    public function form(Schema $schema): Schema {
        return $schema
            ->components([
                Form::make([
                    Section::make(__('Identity'))
                        ->description(__('Who the site represents — feeds the WebSite and Person/Organization schema.'))
                        ->schema([
                            Select::make('type')
                                ->label(__('Identity type'))
                                ->options([
                                    SchemaType::PERSON->value => SchemaType::PERSON->getLabel(),
                                    SchemaType::ORGANIZATION->value => SchemaType::ORGANIZATION->getLabel(),
                                ])
                                ->required(),

                            TextInput::make('name')
                                ->label(__('Name'))
                                ->maxLength(255)
                                ->placeholder(config()->string('app.name')),

                            Tabs::make('description')
                                ->tabs(array_map($this->localeDescriptionTab(...), $this->locales())),

                            Repeater::make('social_profiles')
                                ->label(__('Social profiles'))
                                ->simple(
                                    TextInput::make('url')
                                        ->url()
                                        ->required()
                                )
                                ->helperText(__('Profile URLs emitted as the schema "sameAs" links.'))
                                ->addActionLabel(__('Add profile'))
                                ->defaultItems(0),
                        ]),

                    Section::make(__('Defaults'))
                        ->schema([
                            Toggle::make('website_schema')
                                ->label(__('Emit WebSite schema')),

                            TextInput::make('default_og_image')
                                ->label(__('Default Open Graph image URL'))
                                ->url(),

                            TextInput::make('title_separator')
                                ->label(__('Title separator'))
                                ->required()
                                ->maxLength(10),
                        ]),
                ])
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label(__('Save'))
                                ->submit('save')
                                ->keyBindings(['mod+s']),
                        ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void {
        /** @var array<string, mixed> $data */
        $data = $this->form->getState();

        $record = $this->getRecord();

        /** @var array<string, string> $descriptions */
        $descriptions = $data['description'] ?? [];
        unset($data['description']);

        $record->fill($data);
        $record->setTranslations('description', array_filter($descriptions, fn (string $value): bool => $value !== ''));
        $record->save();

        Notification::make()
            ->success()
            ->title(__('SEO settings saved'))
            ->send();
    }

    public function getRecord(): SeoSetting {
        return SeoSetting::query()->firstOrNew();
    }

    /**
     * One tab editing the translatable description for a single locale.
     */
    private function localeDescriptionTab(string $locale): Tab {
        return Tab::make(strtoupper($locale))
            ->schema([
                Textarea::make("description.{$locale}")
                    ->label(__('Description'))
                    ->rows(3),
            ]);
    }

    /**
     * @return list<string>
     */
    private function locales(): array {
        return array_keys(App::supportedLocales());
    }

    /**
     * @return array<string, string>
     */
    private function translationsFor(SeoSetting $record, string $attribute): array {
        $stored = $record->getTranslations($attribute);
        $translations = [];

        foreach ($this->locales() as $locale) {
            $value = $stored[$locale] ?? '';
            $translations[$locale] = is_string($value) ? $value : '';
        }

        return $translations;
    }
}
