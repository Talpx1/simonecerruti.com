<?php

declare(strict_types=1);

namespace App\Filament\Resources\Concerns;

use Illuminate\Support\Str;

trait HasLocalizedLabels {
    private static function getResourceSlug(): string {
        return Str::snake(str_replace('Resource', '', class_basename(__CLASS__)));
    }

    public static function getModelLabel(): string {
        $resource_slug = static::getResourceSlug();

        return __("resources.{$resource_slug}.label");
    }

    public static function getPluralModelLabel(): string {
        $resource_slug = static::getResourceSlug();

        return __("resources.{$resource_slug}.plural_label");
    }
}
