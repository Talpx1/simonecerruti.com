<?php

declare(strict_types=1);

namespace App\Models\Concerns\Scopes;

use Illuminate\Contracts\Database\Eloquent\Builder;

trait HasCurrentLocaleTranslationScope {
    public function scopeWhereHasCurrentLocaleTranslation(Builder $query, string $field_to_check = 'slug'): void {
        $query
            ->whereNotNull("{$field_to_check}->".app()->getLocale())
            ->where("{$field_to_check}->".app()->getLocale(), '!=', '');
    }
}
