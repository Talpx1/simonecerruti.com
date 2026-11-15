<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Enums\GeneratableFields;
use App\Settings\AiSettings;
use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

final class ManageAiSettings extends SettingsPage {
    protected static string $settings = AiSettings::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    public static function getNavigationLabel(): string {
        return __('AI settings');
    }

    public function getTitle(): string {
        return __('AI settings');
    }

    public function form(Schema $schema): Schema {
        return $schema->components([
            Section::make(__('Style guide'))
                ->description(__('General instructions sent to the AI with every request: voice, tone and rules to follow.'))
                ->schema([
                    Textarea::make('style_guide')
                        ->hiddenLabel()
                        ->required()
                        ->rows(8),
                ]),

            ...$this->makePromptSections(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array {
        /** @var array<string, string|null> $prompts */
        $prompts = $data['prompts'] ?? [];

        $data['prompts'] = \array_filter($prompts, fn (?string $prompt): bool => \filled($prompt));

        return $data;
    }

    /**
     * @return list<Section>
     */
    private function makePromptSections(): array {
        $fields_by_group = collect(GeneratableFields::cases())->groupBy(fn (GeneratableFields $field): string => $field->getGroupLabel());

        return \array_values($fields_by_group
            ->map(fn ($fields, string $group): Section => Section::make($group)
                ->collapsed()
                ->schema($fields->map(fn (GeneratableFields $field): Textarea => Textarea::make("prompts.{$field->value}")
                    ->label($field->getLabel())
                    ->placeholder($field->getDefaultPrompt())
                    ->helperText(__('Leave blank to use the default prompt.'))
                    ->rows(3))->all()))
            ->all());
    }
}
