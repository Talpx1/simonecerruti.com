<?php

declare(strict_types=1);

namespace App\Filament\Components;

use App\Enums\RobotsDirective;
use App\Enums\SchemaType;
use App\Enums\TwitterCard;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Support\Collection;

/**
 * The SEO override fields, bound to a model's `seo` relationship. Every field is
 * optional: left blank, the public pages fall back to the model's declared SEO
 * defaults (HasSeo::seoDefaults). Most fields are translatable and follow the
 * active locale of the surrounding translatable resource.
 */
class SeoFields {
    /**
     * @param  list<string>  $variables  the {placeholders} this model exposes, shown as a hint
     */
    public static function make(array $variables = []): Group {
        return Group::make()
            // Only persist a Seo row when at least one field is filled; an
            // all-blank section saves nothing (and prunes an existing empty row),
            // keeping "no overrides" as the absence of a row.
            ->relationship('seo', condition: fn (?array $state): bool => self::hasAnyOverride($state))
            ->schema([
                Tabs::make('seo')
                    ->columnSpanFull()
                    ->tabs([
                        self::generalTab($variables),
                        self::socialTab(),
                        self::advancedTab(),
                        self::schemaTab(),
                    ]),
            ]);
    }

    /**
     * @param  list<string>  $variables
     */
    private static function generalTab(array $variables): Tab {
        return Tab::make(__('General'))
            ->schema([
                TextInput::make('title')
                    ->label(__('Meta title'))
                    ->maxLength(255)
                    ->hint(self::variablesHint($variables)),

                Textarea::make('description')
                    ->label(__('Meta description'))
                    ->rows(3)
                    ->maxLength(255),
            ]);
    }

    private static function socialTab(): Tab {
        return Tab::make(__('Social'))
            ->schema([
                TextInput::make('og_title')
                    ->label(__('Open Graph title'))
                    ->maxLength(255),

                Textarea::make('og_description')
                    ->label(__('Open Graph description'))
                    ->rows(2),

                TextInput::make('og_image')
                    ->label(__('Open Graph image URL'))
                    ->url(),

                TextInput::make('twitter_title')
                    ->label(__('Twitter title'))
                    ->maxLength(255),

                Textarea::make('twitter_description')
                    ->label(__('Twitter description'))
                    ->rows(2),

                TextInput::make('twitter_image')
                    ->label(__('Twitter image URL'))
                    ->url(),

                Select::make('twitter_card')
                    ->label(__('Twitter card'))
                    ->options(TwitterCard::class)
                    ->placeholder(TwitterCard::SUMMARY_LARGE_IMAGE->getLabel()),
            ]);
    }

    private static function advancedTab(): Tab {
        return Tab::make(__('Advanced'))
            ->schema([
                TextInput::make('canonical')
                    ->label(__('Canonical URL'))
                    ->url(),

                CheckboxList::make('robots')
                    ->label(__('Robots directives'))
                    ->options(RobotsDirective::class)
                    ->columns(2)
                    ->helperText(__('Leave empty to index and follow. Note: noindex also removes the page from the sitemap.')),
            ]);
    }

    private static function schemaTab(): Tab {
        return Tab::make(__('Schema'))
            ->schema([
                Select::make('schema_type')
                    ->label(__('Schema type'))
                    ->options(SchemaType::class)
                    ->placeholder(__('Use the default for this content')),

                KeyValue::make('schema_overrides')
                    ->label(__('Schema property overrides'))
                    ->keyLabel(__('Property'))
                    ->valueLabel(__('Value or template'))
                    ->helperText(__('Override individual schema.org properties; leave a property out to keep its automatic value.')),
            ]);
    }

    /**
     * Whether the SEO section holds at least one override, ignoring the Seo
     * row's own keys/timestamps. Drives whether a row is saved or pruned.
     *
     * @param  array<array-key, mixed>|null  $state
     */
    private static function hasAnyOverride(?array $state): bool {
        return Collection::make($state ?? [])
            ->except(['id', 'seoable_type', 'seoable_id', 'created_at', 'updated_at'])
            ->contains(fn (mixed $value): bool => filled($value));
    }

    /**
     * @param  list<string>  $variables
     */
    private static function variablesHint(array $variables): ?string {
        if ($variables === []) {
            return null;
        }

        return __('Available variables: :vars', [
            'vars' => Collection::make($variables)->map(fn (string $variable): string => '{'.$variable.'}')->implode(', '),
        ]);
    }
}
