<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use Illuminate\Support\Facades\App;

trait ResolvesRoutBindingByLocalizedSlug {
    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null): static {
        $current_locale = App::currentLocale();

        return static::query()->where("slug->{$current_locale}", $value)->firstOrFail();
    }
}
