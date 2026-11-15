<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Tag;
use Illuminate\Support\Facades\App;
use Lorisleiva\Actions\Concerns\AsAction;

final class FillMissingTagTranslations {
    use AsAction;

    public function handle(Tag $tag): Tag {
        $names = $tag->getTranslations('name');

        $source_locale = \array_key_exists(App::getLocale(), $names) ? App::getLocale() : \array_key_first($names);

        if ($source_locale === null) {
            return $tag;
        }

        foreach (Tag::getTranslatableLocales() as $locale) {
            if (! \array_key_exists($locale, $names)) {
                $tag->setTranslation('name', $locale, $names[$source_locale]);
            }
        }

        if ($tag->isDirty()) {
            $tag->save();
        }

        return $tag;
    }
}
