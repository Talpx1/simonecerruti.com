<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Uri;
use Spatie\Sitemap\Tags\Url;

trait HasSitemapTag {
    abstract protected function getSitemapRoute(string $locale): string;

    abstract protected function getSitemapPriority(): float;

    abstract protected function getSitemapChangeFrequency(): string;

    /**
     * @return Url|string|list<Url>
     */
    public function toSitemapTag(): Url|string|array {
        $can_add_to_sitemap = method_exists($this, 'canAddToSitemap')
            ? $this->canAddToSitemap()
            : true;

        if (! $can_add_to_sitemap) {
            return '';
        }

        /** @var list<string> $locales */
        $locales = $this->locales();

        // Resolve indexability once per locale; both the main loop and the
        // alternates reuse this map instead of re-checking each locale twice.
        $indexable = [];
        foreach ($locales as $locale) {
            $indexable[$locale] = ! method_exists($this, 'isIndexable') || $this->isIndexable($locale);
        }

        $urls = [];

        foreach ($locales as $locale) {
            if (! $indexable[$locale]) {
                continue;
            }

            /** @var Uri */
            $uri = Route::localizedUrl($locale, $this->getSitemapRoute($locale));

            $url = Url::create($uri->__toString())
                ->setPriority($this->getSitemapPriority())
                ->setChangeFrequency($this->getSitemapChangeFrequency());

            $url = $this->attachSitemapAlternates($url, $locale, $indexable);
            $urls[] = $url;
        }

        return $urls;
    }

    /**
     * @param  array<string, bool>  $indexable  indexability per locale, resolved once by the caller
     */
    private function attachSitemapAlternates(Url $url, string $current_locale, array $indexable): Url {
        /** @var list<string> $locales */
        $locales = $this->locales();

        foreach ($locales as $alternate) {
            if ($alternate === $current_locale || ! $indexable[$alternate]) {
                continue;
            }

            /** @var Uri */
            $uri = Route::localizedUrl($alternate, $this->getSitemapRoute($alternate));
            $url->addAlternate($uri->__toString(), $alternate);
        }

        return $url;
    }
}
