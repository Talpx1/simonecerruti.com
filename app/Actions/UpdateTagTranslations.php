<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Tag;
use Illuminate\Support\Facades\Gate;
use Lorisleiva\Actions\Concerns\AsAction;

final class UpdateTagTranslations {
    use AsAction;

    /**
     * @param  array<string, string|null>  $names_by_locale
     */
    public function handle(Tag $tag, array $names_by_locale): Tag {
        Gate::authorize('translateTags');

        foreach (Tag::getTranslatableLocales() as $locale) {
            $name = $names_by_locale[$locale] ?? null;

            if (\filled($name)) {
                $tag->setTranslation('name', $locale, $name);
            }
        }

        $tag->save();

        return $tag;
    }
}
