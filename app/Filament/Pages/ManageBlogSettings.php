<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Resources\BlogArticles\BlogArticleResource;
use App\Settings\BlogSettings;
use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\App;

final class ManageBlogSettings extends SettingsPage {
    protected static string $settings = BlogSettings::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationParentItem = BlogArticleResource::class;

    public static function getNavigationLabel(): string {
        return __('Settings');
    }

    public function getTitle(): string {
        return __('Settings');
    }

    public function form(Schema $schema): Schema {
        return $schema->components([
            Tabs::make('author_bio')
                ->columnSpanFull()
                ->tabs(array_map($this->makeLocaleTab(...), array_keys(App::supportedLocales()))),
        ]);
    }

    private function makeLocaleTab(string $locale): Tab {
        return Tab::make(\strtoupper($locale))
            ->schema([
                Textarea::make("author_bio.{$locale}")
                    ->label(__('Author bio'))
                    ->helperText(__('Shown under blog articles.'))
                    ->rows(5),
            ]);
    }
}
