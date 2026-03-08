<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Exceptions\MissingRoutableLocalizedRouteKeyException;

trait HasRoutableSlug {
    /** @param string $locale */
    public function getLocalizedRouteKey($locale): mixed {
        $translations = $this->getTranslations('slug');
        $slug = $translations[$locale] ?? false;

        throw_if(! $slug, MissingRoutableLocalizedRouteKeyException::class, 'slug', $locale, $this);

        return $slug;
    }
}
