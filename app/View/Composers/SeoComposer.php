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
                canonical: url()->current(),
                robots: $this->robots($data),
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
        $title = isset($data['title']) && is_string($data['title']) && $data['title'] !== ''
            ? $data['title']
            : $app_name;

        if (! isset($data['suffix'])) {
            return $title.$separator.$app_name;
        }

        return is_string($data['suffix'])
            ? $title.$separator.$data['suffix']
            : $title;
    }

    /**
     * Optional robots directives a page may opt into (e.g. 'noindex' on the
     * legal pages). Pages pass it as a layout variable; when absent no robots
     * meta tag is emitted at all.
     *
     * @param  array<array-key, mixed>  $data
     */
    private function robots(array $data): ?string {
        return isset($data['robots']) && is_string($data['robots']) && $data['robots'] !== ''
            ? $data['robots']
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
