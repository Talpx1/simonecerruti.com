<?php

declare(strict_types=1);

namespace App\View\Composers;

use App\DataTransferObjects\SeoData;
use App\Models\SeoSetting;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

/**
 * Provides the public layout with a default `$seo_data` (title, canonical and
 * hreflang alternates) when a page has not supplied its own resolved SeoData.
 * Richer, per-model SeoData is injected by pages and short-circuits this default.
 */
class SeoComposer {
    public function compose(View $view): void {
        $data = $view->getData();

        // A page may supply its own resolved SeoData (show pages do); otherwise
        // fall back to a minimal default built from the page title.
        $seo_data = isset($data['seo_data']) && $data['seo_data'] instanceof SeoData
            ? $data['seo_data']
            : new SeoData(
                title: $this->title($data),
                description: $this->stringField($data, 'description'),
                canonical: url()->current(),
                robots: $this->stringField($data, 'robots'),
                alternates: $this->alternates(),
            );

        // The sitewide identity (WebSite + Person/Organization) is prepended to
        // every page's @graph, whether the SeoData is page-provided or default.
        $view->with('seo_data', $seo_data->prependJsonLd(SeoSetting::current()->schemaNodes()));
    }

    /**
     * Mirrors the layout's previous title logic: append the app name unless a
     * `suffix` was provided, in which case use it (or omit it entirely on false).
     *
     * @param  array<array-key, mixed>  $data
     */
    private function title(array $data): string {
        $separator = $this->separator();
        $app_name = config()->string('app.name');
        $title = $this->stringField($data, 'title') ?? $app_name;

        if (! isset($data['suffix'])) {
            return $title.$separator.$app_name;
        }

        return is_string($data['suffix'])
            ? $title.$separator.$data['suffix']
            : $title;
    }

    /**
     * A non-empty string value from the page data for the given key, or null
     * when the key is absent, not a string, or empty. Reads optional layout
     * variables such as the page `title` or `robots` directives.
     *
     * @param  array<array-key, mixed>  $data
     */
    private function stringField(array $data, string $key): ?string {
        return isset($data[$key]) && is_string($data[$key]) && $data[$key] !== ''
            ? $data[$key]
            : null;
    }

    /**
     * Title separator, from the cached global settings (no per-request DB hit).
     */
    private function separator(): string {
        return SeoSetting::current()->title_separator;
    }

    /**
     * One hreflang alternate per supported locale, pointing at the current page
     * in that locale — identical to the layout's previous alternates loop.
     *
     * @return list<array{hreflang: string, href: string}>
     */
    private function alternates(): array {
        $alternates = [];

        foreach (array_keys(App::supportedLocales()) as $locale) {
            $alternates[] = [
                'hreflang' => $locale,
                'href' => Route::localizedUrlString(locale: $locale, force_default_location: true),
            ];
        }

        return $alternates;
    }
}
