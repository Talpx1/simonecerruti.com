<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\DataTransferObjects\SeoConfig;
use App\DataTransferObjects\SeoData;
use App\Enums\RobotsDirective;
use App\Models\Seo;
use App\Models\SeoSetting;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

/**
 * Gives a model resolved, translatable SEO: it merges the model's declared
 * defaults (defaultSeoConfig) with the stored per-record overrides (the Seo
 * relation), resolves {variables} via Str::replacePlaceholders, and produces a
 * SeoData for the current locale. Crawl rules (status + SEO noindex) are
 * reconciled in one place (isIndexable / the robots meta).
 */
trait HasSeo {
    /**
     * The model's SEO defaults (templates, schema mapping, default robots).
     */
    abstract public function seoDefaults(): SeoConfig;

    /**
     * Template variables for this model in the current locale, e.g.
     * ['title' => $this->title, 'excerpt' => $this->summary_or_excerpt].
     *
     * @return array<string, string>
     */
    abstract protected function seoVariables(): array;

    /**
     * Whether the model is crawlable by its own status rules (publication,
     * status enum, …) — i.e. ignoring the per-record SEO noindex override.
     */
    abstract protected function isCrawlableByStatus(): bool;

    /**
     * The localized public route for the given locale. Also required by
     * HasSitemapTag, which every SEO-aware model already uses.
     */
    abstract protected function getSitemapRoute(string $locale): string;

    /**
     * The per-record SEO overrides. withDefault() yields an empty (non-persisted)
     * Seo when no row exists, so $this->seo is always a Seo whose every field is
     * null — each one then falls back to the model's declared defaults.
     *
     * @return MorphOne<Seo, $this>
     */
    public function seo(): MorphOne {
        return $this->morphOne(Seo::class, 'seoable')->withDefault();
    }

    public function toSeoData(): SeoData {
        $defaults = $this->seoDefaults();
        $seo = $this->seo;
        $vars = $this->resolvedSeoVariables();

        $title = $this->resolveTemplate($this->override($seo, 'title') ?? $defaults->title, $vars);
        $description = $this->resolveTemplate($this->override($seo, 'description') ?? $defaults->description, $vars);
        $og_title = $this->resolveTemplate($this->override($seo, 'og_title') ?? $defaults->og_title, $vars) ?: $title;
        $og_description = $this->resolveTemplate($this->override($seo, 'og_description') ?? $defaults->og_description, $vars) ?: $description;
        $og_image = $this->resolveTemplate($this->override($seo, 'og_image') ?? $defaults->og_image, $vars);
        $twitter_title = $this->resolveTemplate($this->override($seo, 'twitter_title') ?? $defaults->twitter_title, $vars) ?: $og_title;
        $twitter_description = $this->resolveTemplate($this->override($seo, 'twitter_description') ?? $defaults->twitter_description, $vars) ?: $og_description;
        $twitter_image = $this->resolveTemplate($this->override($seo, 'twitter_image') ?? $defaults->twitter_image, $vars) ?: $og_image;
        $card = ($seo->twitter_card ?? $defaults->twitter_card)->value;

        $canonical = $this->override($seo, 'canonical')
            ?: $this->localizedUrl(App::getLocale());

        return new SeoData(
            title: $title ?: null,
            description: $description ?: null,
            canonical: $canonical,
            robots: $this->resolveRobots($seo, $defaults),
            alternates: $this->seoAlternates(),
            open_graph: $this->openGraphTags($og_title, $og_description, $og_image, $canonical),
            twitter: $this->twitterTags($card, $twitter_title, $twitter_description, $twitter_image),
            json_ld: [$this->primarySchemaNode($defaults, $seo, $vars)],
        );
    }

    /**
     * Whether the model may be indexed in the given locale: crawlable by status
     * AND not flagged noindex via its SEO overrides for that locale.
     */
    public function isIndexable(?string $locale = null): bool {
        return $this->isCrawlableByStatus() && ! $this->hasSeoNoindex($locale ?? App::getLocale());
    }

    private function hasSeoNoindex(string $locale): bool {
        $robots = $this->seo->getTranslation('robots', $locale, false);

        return is_array($robots) && in_array(RobotsDirective::NOINDEX->value, $robots, true);
    }

    /**
     * A stored override for a translatable string field in the current locale,
     * or null when unset there. Read WITHOUT the spatie fallback locale, so an
     * override set only in another locale does not leak in — the field falls
     * through to the model's default template instead.
     */
    private function override(Seo $seo, string $field): ?string {
        $value = $seo->getTranslation($field, App::getLocale(), false);

        return is_string($value) && $value !== '' ? $value : null;
    }

    /**
     * @return array<string, string>
     */
    private function resolvedSeoVariables(): array {
        return [
            ...$this->globalSeoVariables(),
            ...$this->seoVariables(),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function globalSeoVariables(): array {
        $settings = SeoSetting::current();

        return [
            'site_name' => $this->siteName(),
            'title_separator' => $settings->title_separator,
            'locale' => App::getLocale(),
            'current_year' => (string) now()->year,
        ];
    }

    /**
     * The configured SEO site name, falling back to the app name.
     */
    private function siteName(): string {
        return SeoSetting::current()->name ?? config()->string('app.name');
    }

    /**
     * @param  array<string, string>  $vars
     */
    private function resolveTemplate(?string $template, array $vars): string {
        if (! $template) {
            return '';
        }

        $resolved = Str::replacePlaceholders($template, $vars, strip_unmatched: true);

        return trim((string) \Safe\preg_replace('/\s+/', ' ', $resolved));
    }

    private function resolveRobots(Seo $seo, SeoConfig $config): ?string {
        // Read robots for the current locale only (no fallback), so a per-locale
        // noindex does not bleed into other locales via the spatie fallback.
        $stored = $seo->getTranslation('robots', App::getLocale(), false);

        $directives = is_array($stored) && $stored !== []
            ? array_values(array_filter($stored, 'is_string'))
            : array_map(fn (RobotsDirective $directive): string => $directive->value, $config->robots);

        if (! $this->isCrawlableByStatus()) {
            $directives[] = RobotsDirective::NOINDEX->value;
        }

        $directives = array_values(array_unique($directives));

        return $directives === [] ? null : implode(',', $directives);
    }

    /**
     * @return list<array{hreflang: string, href: string}>
     */
    private function seoAlternates(): array {
        $alternates = [];

        /** @var list<string> $locales */
        $locales = $this->locales();

        foreach ($locales as $locale) {
            $alternates[] = [
                'hreflang' => $locale,
                'href' => $this->localizedUrl($locale),
            ];
        }

        return $alternates;
    }

    private function localizedUrl(string $locale): string {
        return Route::localizedUrlString($locale, $this->getSitemapRoute($locale));
    }

    /**
     * @return array<string, string>
     */
    private function openGraphTags(string $title, string $description, string $image, string $url): array {
        $regional = LaravelLocalization::getCurrentLocaleRegional() ?: App::getLocale();

        return array_filter([
            'og:type' => 'article',
            'og:title' => $title,
            'og:description' => $description,
            'og:url' => $url,
            'og:site_name' => $this->siteName(),
            'og:locale' => $regional,
            'og:image' => $image,
        ], fn (string $value): bool => $value !== '');
    }

    /**
     * @return array<string, string>
     */
    private function twitterTags(string $card, string $title, string $description, string $image): array {
        return array_filter([
            'twitter:card' => $card,
            'twitter:title' => $title,
            'twitter:description' => $description,
            'twitter:image' => $image,
        ], fn (string $value): bool => $value !== '');
    }

    /**
     * The page's primary schema.org node (e.g. BlogPosting/CreativeWork), built
     * from the schema property templates with stored per-property overrides.
     *
     * @param  array<string, string>  $vars
     * @return array<string, mixed>
     */
    private function primarySchemaNode(SeoConfig $config, Seo $seo, array $vars): array {
        $type = ($seo->schema_type ?? $config->schema_type)->schemaOrgType();

        // Per-property overrides for the current locale only (no fallback).
        $stored = $seo->getTranslation('schema_overrides', App::getLocale(), false);
        $overrides = is_array($stored) ? $stored : [];

        $node = ['@type' => $type];

        foreach ($config->schema as $property => $template) {
            $override = $overrides[$property] ?? null;
            $value = $this->resolveTemplate(is_string($override) ? $override : $template, $vars);

            if ($value !== '') {
                $node[$property] = $value;
            }
        }

        return $node;
    }
}
