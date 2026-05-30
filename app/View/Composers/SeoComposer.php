<?php

declare(strict_types=1);

namespace App\View\Composers;

use App\DataTransferObjects\SeoData;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Uri;
use Illuminate\View\View;

/**
 * Provides the public layout with a default `$seo_data` (title, canonical and
 * hreflang alternates) when a page has not supplied its own resolved SeoData.
 * Richer, per-model SeoData is injected by pages and short-circuits this default.
 */
class SeoComposer {
    public function compose(View $view): void {
        $data = $view->getData();

        // A page (or a later phase) may already provide its own resolved SeoData.
        if (isset($data['seo_data'])) {
            return;
        }

        $view->with('seo_data', new SeoData(
            title: $this->title($data),
            canonical: url()->current(),
            alternates: $this->alternates(),
        ));
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
     * Title separator. Fase 2 will source this from the cached global
     * `SeoSetting::current()->title_separator` (cached, so no per-request DB hit).
     */
    private function separator(): string {
        return ' | ';
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
            /** @var Uri $uri */
            $uri = Route::localizedUrl(locale: $locale, force_default_location: true);

            $alternates[] = [
                'hreflang' => $locale,
                'href' => $uri->__toString(),
            ];
        }

        return $alternates;
    }
}
