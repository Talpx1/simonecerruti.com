<?php

declare(strict_types=1);

namespace App\Filament\Components;

use App\Actions\UpdateTagTranslations;
use App\Models\Tag;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

final class TranslateTagsAction {
    public static function make(): Action {
        return Action::make('translateTags')
            ->label(__('Translate tags'))
            ->icon(Heroicon::OutlinedLanguage)
            ->modalHeading(__('Translate tags'))
            ->modalSubmitActionLabel(__('Save translations'))
            ->visible(fn (SpatieTagsInput $component): bool => Gate::allows('translateTags') && \filled($component->getState()))
            ->fillForm(fn (SpatieTagsInput $component): array => ['tags' => self::getRows($component)])
            ->schema([
                Repeater::make('tags')
                    ->hiddenLabel()
                    ->addable(false)
                    ->deletable(false)
                    ->reorderable(false)
                    ->schema([Hidden::make('id'), ...self::getNameInputs()])
                    ->columns(2),
            ])
            ->action(function (array $data, SpatieTagsInput $component, Set $set): void {
                /** @var list<array{id: int, names: array<string, string|null>}> $rows */
                $rows = $data['tags'];

                $names = [];

                foreach ($rows as $row) {
                    $names[] = UpdateTagTranslations::run(Tag::query()->findOrFail($row['id']), $row['names'])->name;
                }

                $set($component->getName(), $names);

                Notification::make()->title(__('Tags translated'))->success()->send();
            });
    }

    /**
     * @return list<TextInput>
     */
    private static function getNameInputs(): array {
        $inputs = [];

        foreach (Tag::getTranslatableLocales() as $locale) {
            $inputs[] = TextInput::make("names.{$locale}")
                ->label(Str::upper($locale))
                ->maxLength(255)
                ->required(fn (): bool => $locale === App::getLocale());
        }

        return $inputs;
    }

    /**
     * @return list<array{id: int, names: array<string, mixed>}>
     */
    private static function getRows(SpatieTagsInput $component): array {
        $rows = [];

        foreach (self::resolveTags($component) as $tag) {
            $names = [];

            foreach (Tag::getTranslatableLocales() as $locale) {
                $names[$locale] = $tag->getTranslationWithoutFallback('name', $locale);
            }

            $rows[] = ['id' => $tag->id, 'names' => $names];
        }

        return $rows;
    }

    /**
     * @return Collection<int, Tag>
     */
    private static function resolveTags(SpatieTagsInput $component): Collection {
        $type = $component->getType();

        /** @var list<string> $names */
        $names = \array_values((array) $component->getState());

        $created = Tag::findOrCreate($names, \is_string($type) ? $type : null);

        return Tag::query()->whereKey(collect($created)->pluck('id')->all())->get();
    }
}
