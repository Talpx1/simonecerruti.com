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

        $urls = [];

        /** @var list<string> $locales */
        $locales = $this->locales();

        foreach ($locales as $locale) {
            $route = $this->getSitemapRoute($locale);

            /** @var Uri */
            $uri = Route::localizedUrl($locale, $route);

            $url = Url::create($uri->__toString())
                ->setPriority($this->getSitemapPriority())
                ->setChangeFrequency($this->getSitemapChangeFrequency());

            $url = $this->attachSitemapAlternates($url, $locale);
            $urls[] = $url;
        }

        return $urls;
    }

    private function attachSitemapAlternates(Url $url, string $current_locale): Url {
        /** @var list<string> $locales */
        $locales = $this->locales();

        foreach ($locales as $alternate) {
            if ($alternate === $current_locale) {
                continue;
            }

            /** @var Uri */
            $uri = Route::localizedUrl($alternate, $this->getSitemapRoute($alternate));
            $url->addAlternate($uri->__toString(), $alternate);
        }

        return $url;
    }
}
