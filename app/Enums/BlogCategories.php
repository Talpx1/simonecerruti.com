<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasLocalizedLabel;
use App\Enums\Concerns\HasModel;
use App\Enums\Concerns\SeedDb;
use App\Enums\Contracts\SyncsToDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Spatie\Tags\Tag;

enum BlogCategories: int implements SyncsToDatabase {
    /** @use HasModel<Tag> */
    use HasLocalizedLabel, HasModel, SeedDb;

    case PRACTICAL = 1;
    case TECHNICAL = 2;

    private function dbMap(): array {
        return [
            'id' => $this->value,

            'name' => collect(array_keys(App::supportedLocales()))
                ->mapWithKeys(fn (string $locale) => [
                    $locale => $this->getLabel($locale),
                ])->toJson(),

            'slug' => collect(array_keys(App::supportedLocales()))
                ->mapWithKeys(fn (string $locale) => [
                    $locale => Str::slug($this->getLabel($locale), language: $locale),
                ])->toJson(),

            'type' => TagTypes::BLOG_CATEGORY->value,

            'order_column' => 1,
        ];
    }

    public static function getModelClass(): string {
        return config()->string('tags.tag_model');
    }

    protected static function partialSync(): bool {
        return true;
    }
}
