<?php

declare(strict_types=1);

namespace App\Filament\Components;

use App\Actions\GenerateFieldContent;
use App\Actions\GetFieldPrompt;
use App\Actions\SaveFieldPrompt;
use App\Data\FieldGenerationData;
use App\Enums\Abilities;
use App\Enums\GeneratableFields;
use Filament\Actions\Action;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\App;
use Laravel\Ai\Exceptions\AiException;
use Laravel\Ai\Exceptions\InsufficientCreditsException;
use Livewire\Component as LivewireComponent;

final class GenerateWithAiAction {
    public static function make(GeneratableFields $field): Action {
        return Action::make('generateWithAi')
            ->label(__('Generate with AI'))
            ->icon(Heroicon::OutlinedSparkles)
            ->color('primary')
            ->authorize(Abilities::GENERATE_AI_CONTENT->value)
            ->modalHeading(__('Generate :field with AI', ['field' => $field->getLabel()]))
            ->modalDescription(__('The generated text replaces the current content of the field. Review it before saving.'))
            ->modalSubmitActionLabel(__('Generate'))
            ->fillForm(fn (): array => ['prompt' => GetFieldPrompt::run($field), 'notes' => null, 'save_as_default' => false])
            ->schema([
                Textarea::make('prompt')
                    ->label(__('Prompt'))
                    ->required()
                    ->rows(8),
                Textarea::make('notes')
                    ->label(__('Notes and raw material'))
                    ->helperText(__('Optional. Add facts, bullet points or a draft the AI must use.'))
                    ->rows(6),
                Toggle::make('save_as_default')
                    ->label(__('Save this prompt as the default for this field')),
            ])
            ->action(function (array $data, Field $component, Get $get, Set $set, LivewireComponent $livewire) use ($field): void {
                /** @var array{prompt: string, notes: ?string, save_as_default: bool} $data */
                $locale = \property_exists($livewire, 'activeLocale') && \is_string($livewire->activeLocale)
                    ? $livewire->activeLocale
                    : App::getLocale();

                $generation = new FieldGenerationData(
                    field: $field,
                    prompt: $data['prompt'],
                    notes: $data['notes'],
                    locale: $locale,
                    current_value: self::stringifyState($component->getState(), $component),
                    context: self::getContext($field, $get),
                );

                try {
                    $text = GenerateFieldContent::run($generation);
                } catch (InsufficientCreditsException) {
                    Notification::make()->title(__('The AI credits are exhausted'))->danger()->send();

                    return;
                } catch (AiException) {
                    Notification::make()->title(__('The AI could not generate the text'))->body(__('Try again in a few moments.'))->danger()->send();

                    return;
                }

                $set($component->getName(), self::toFieldState($text, $component));

                if ($data['save_as_default']) {
                    SaveFieldPrompt::run($field, $data['prompt']);
                }

                Notification::make()->title(__('Text generated'))->success()->send();
            });
    }

    /**
     * @return array<string, string>
     */
    private static function getContext(GeneratableFields $field, Get $get): array {
        $context = [];

        foreach ($field->getContextFields() as $label => $path) {
            $value = self::stringifyState($get($path));

            if ($value !== null) {
                $context[$label] = $value;
            }
        }

        return $context;
    }

    private static function stringifyState(mixed $state, ?Field $component = null): ?string {
        $text = match (true) {
            \is_array($state) => self::renderDocument(\array_filter($state, \is_string(...), ARRAY_FILTER_USE_KEY), $component),
            \is_string($state) => $state,
            default => null,
        };

        return \filled($text) ? \trim($text) : null;
    }

    /**
     * @param  array<string, mixed>  $document
     */
    private static function renderDocument(array $document, ?Field $component): string {
        $renderer = RichContentRenderer::make($document);

        return $component instanceof RichEditor
            ? $renderer->plugins($component->getPlugins())->toHtml()
            : $renderer->toText();
    }

    /**
     * @return array<string, mixed>|string
     */
    private static function toFieldState(string $text, Field $component): array|string {
        if (! $component instanceof RichEditor) {
            return $text;
        }

        return RichContentRenderer::make($text)->plugins($component->getPlugins())->toArray();
    }
}
