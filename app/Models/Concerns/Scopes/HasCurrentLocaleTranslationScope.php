<?php

declare(strict_types=1);

namespace App\Models\Concerns\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait HasCurrentLocaleTranslationScope {
    /** @param Builder<Model> $query */
    protected function scopeWhereHasCurrentLocaleTranslation(Builder $query, string $field_to_check = 'slug'): void {
        $query
            ->whereNotNull("{$field_to_check}->".app()->getLocale())
            ->where("{$field_to_check}->".app()->getLocale(), '!=', '');
    }
}
