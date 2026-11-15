# Public site

How the public pages are built: the frontend runtime, the data behind the back-office, translations and tracked elements.

## Frontend runtime

`resources/js/public.js` is the single entry point for every public page. It exposes `window.siteRuntime` with shared helpers (`prefersReducedMotion`, `hasFinePointer`, `clamp`, `linearInterpolate`, `smoothStep`, `queryOne`, `queryAll`, `pointer`) and `siteRuntime.frameCallbacks`, an array of callbacks run on each animation frame as `callback(timestamp, deltaSeconds)`.

Two flags drive every effect: `siteRuntime.prefersReducedMotion` (`prefers-reduced-motion`) and `siteRuntime.hasFinePointer` (a precise pointer). Effects that need the mouse or heavy motion must check them.

### Page lifecycle

The site uses `wire:navigate`, so the document is never reloaded between pages.

- `livewire:navigating` calls `resetPage()`: it runs every registered cleanup, empties `siteRuntime.frameCallbacks` and resets the per-page state.
- `livewire:navigated` calls `refreshPage()`: it rebinds the menu, copy buttons, reveals, scroll-lit statements, the logo and every `data-effect` module. `refreshPage()` skips work if `<main>` is the same element as the last run.

Anything attached to `document`, `window` or an observer must be removed in a cleanup, or it will leak across navigations.

### `data-effect` modules

A `data-effect="name"` attribute on an element loads `resources/js/pages/name.js` through `import.meta.glob`, so each module is its own chunk and only loads on pages that use it.

To add one:

1. Create `resources/js/pages/<name>.js` with a default export `function (element, siteRuntime)`.
2. Put `data-effect="<name>"` on the root element in Blade.
3. Return a cleanup function. It runs on the next `wire:navigate` and must undo listeners, observers and timers. Push per-frame work onto `siteRuntime.frameCallbacks` (it is cleared automatically).

```js
export default function (element, siteRuntime) {
    const accordionItems = siteRuntime.queryAll('.accordion-item', element);
    const removeListeners = accordionItems.map((accordionItem) => {
        const toggleItem = () => accordionItem.classList.toggle('is-open');
        accordionItem.addEventListener('click', toggleItem);
        return () => accordionItem.removeEventListener('click', toggleItem);
    });

    return () => removeListeners.forEach((removeListener) => removeListener());
}
```

The module is skipped if `element` is no longer connected when it finishes loading. `faq.js` is the smallest reference.

### Reveals and other markup hooks

- `.reveal`: the element slides up and fades in the first time it enters the viewport (an `IntersectionObserver` adds `.is-revealed`). With reduced motion it is shown immediately.
- `[data-variable-letters]`: text is split into letters that react to the cursor (variable font weight).
- `.scroll-lit p`: words light up as the paragraph scrolls; `data-accent-pattern` is a regex marking accent words.
- `.magnetic-hover`: magnetic hover. `[data-cursor]`: label shown inside the custom cursor.
- `[data-copy]`: copies the value to the clipboard (`data-copied` is the confirmation label).

### Persisted layers

The cursor, grain, vignette and progress bar live in `#persisted-effect-layers`, wrapped in `@persist('persisted-effect-layers')` in `layouts/public/index.blade.php`. Livewire keeps them across navigations, so they must not be re-created by modules. The `requestAnimationFrame` loop in `public.js` drives them and is started once.

### Home canvas and three.js

Only the home page owns the `#home-webgl-canvas` canvas (`pages/home/home.blade.php`). `three` is imported dynamically from `pages/home.js`, so no other page downloads it. If the import or WebGL initialisation fails, the `is-webgl-unavailable` class is set on the root and the page falls back to its static look. Do not add a second canvas or a static `three` import elsewhere.

## CSS organisation

`resources/css/app.css` is the only stylesheet entry. It imports Tailwind, the Filament styles, then `site.css`, `cookie.css` and `pages/*.css`, and defines the theme tokens (`--color-*`, `--font-*`) as Tailwind theme variables.

- `site.css`: design tokens on `:root` (`--site-color-background`, `--site-color-text`, `--site-color-text-muted`, `--site-color-accent`, `--site-gutter`, ...), fixed layers, navigation, buttons and shared components.
- `pages/<page>.css`: styles scoped to a single page or feature (`home`, `blog`, `contacts`, `legal`, `cookie-consent`). A new page style goes in its own file, imported at the end of `app.css`.
- Use the tokens instead of literal colours.
- Class names are long and speak for themselves: a component prefix, then the part (`cookie-banner-accept`, `home-works-item-name`), `--` for variants (`section--tight`) and `is-*` for states (`is-active`, `is-open`, `is-visible`). Never abbreviate (`ck`, `rv`, `ph1`). JavaScript variables follow the same rule.

Fonts are self-hosted: Archivo Variable comes from `@fontsource-variable/archivo` (imported in `app.css`, `wdth` axis included), so no request goes to Google Fonts.

## Back-office data model

### Primary keys

Every app model uses a UUIDv7 primary key (`HasUuids`) and logs its changes (`LogsAllDirtyChanges`); every column that points to an app model, morph columns included (`media.model_id`, `taggables.taggable_id`, `activity_log.subject_id`/`causer_id`, `seo.seoable_id`, ...), is a `uuid`. Vendor tables (`tags`, `media`, `activity_log`, `settings`, ...) keep their own integer keys, so `Tag` cannot log changes: its integer id does not fit `activity_log.subject_id`.

The keys used to be integers. The `convert_primary_keys_to_uuid` migration converted them in place, in one transaction:

- the new ids are UUIDv7 values in the same order as the old integer ids and older than any id created afterwards, so `orderBy('id')` keeps its meaning;
- every foreign key and morph column is remapped through `(type, old id)`; the migration aborts, changing nothing, when a NOT NULL reference points to a missing record, and sets a nullable one to `NULL`;
- foreign keys, unique constraints and indexes dropped with the old columns are re-created from the definitions read before the conversion;
- the ids inside the activity log `attribute_changes` are remapped when the record still exists;
- authenticated database sessions are deleted, because their payload holds the old integer user id;
- `media.legacy_model_id` keeps the old integer id of the owner: `MediaPathGenerator` uses it before `model_id`, so files uploaded before the conversion keep their path (`projects/5/...`) and new files use the UUID.

### Client

`clients` table, `Client` model: `name` and a translatable `description`. A client has many projects (`projects.client_id`). Each client can have one square image (`client_image` media collection, single file): the `client_image_webp` conversion crops it to a 600x600 webp, tuned in `config/client.php`, and `image_url` returns its URL or `null`. The project page shows it in the Client block when present. Managed in the Filament Client resource, which includes the image upload. Deleting goes through the `DeleteClient` action, which first nulls `client_id` on the client's projects so no project is lost.

### Project

On top of the usual fields, the Project form has the case-study fields shown on the project page:

| Field | Use |
|---|---|
| `client_id` | Relation to `Client` (replaces the old free-text client name; a migration moved the existing names into `clients`). |
| `starting_point`, `goal`, `outcome` | The three narrative blocks of the case study. |
| Kinds | Short labels such as "Management software", stored as tags of type `project_kind` (`TagTypes::PROJECT_KIND`) and edited in the "Tipologia" tags input. `kind_names` joins them with ` + `, or returns an empty string when there are none. There is no fallback to the other tags. On the public projects list the kinds are also filter options and are part of each card's `data-types`. |
| `features` | Repeater of `{title, description}`. |
| `home_summary` | Short text for the home page. |

Related actions: `GetAdjacentProjects` and `GetServiceAreaProjects`. Actions return DTOs, never arrays with PHPDoc shapes. Every DTO lives in `App\Data` and extends `Spatie\LaravelData\Data` (`spatie/laravel-data`), with `public readonly` promoted properties. Classes cannot be `readonly` as a whole, because the `Data` base class is not.

`GetAdjacentProjects` returns an `AdjacentProjectsData` with a nullable `previous` and `next` project. Navigation is circular, so a side is null only when there is no other content. With exactly two items, the first has only `next` and the last only `previous`. The current item is never returned as its own neighbour. The navigation is hidden when both are null. Articles follow the same previous/next rule.

The home page works section is built from a list of `WorkCardData` (title, kind, image URL, summary HTML, URL).

### Tag

Tags have a `description` and an optional `service`, a `ServiceAreas` case (`management_erp_crm`, `web_ecommerce_platforms`, `consulting_and_seo`). `GetServiceAreaProjects::run($area, $limit)` returns the featured projects that carry a tag with that service; the matching service landing page (`$area->getRouteName()`) uses it for its project section. To show a project on a landing page, tag it with a tag whose `service` is set.

Every tag has a name in every supported locale, so the public site never meets a tag without a slug. `Tag::findOrCreate()` (used by every tags input) runs `FillMissingTagTranslations`, which copies the name into the locales that have none; the slug follows from the name when the tag is saved. Until someone translates it, the English site shows the Italian name. The "Translate tags" action beside each tags input opens a modal with one name field per locale for the tags of that field, and saves them through `UpdateTagTranslations`.
### Blog

- `BlogSettings` (Filament settings page, group `blog`) holds the per-locale `author_bio`, shown in the article footer through `getAuthorBioForCurrentLocale()`; it is null when empty.
- The list is paginated (`BlogArticleList::ARTICLES_PER_PAGE`, 6).
- `GetFeaturedBlogArticles` returns the two latest published articles flagged `featured` that have a translation for the current locale.
- `AddHeadingIds` adds ids to the `h2`/`h3` of the rendered article HTML. `BuildTableOfContents` is a pure read that returns a list of `TableOfContentsEntryData` (id, title, level), used by the article sidebar. `CalculateReadingTime` gives the reading time.
- The rich editor has a `Gallery` block (`app/Filament/RichContentBlocks/GalleryBlock.php`): 2 to 12 images with optional captions, stored in `blog/gallery` on the public disk. The preview view is `rich-content.blocks.gallery-preview`.

## Translations

The English phrase is the translation key: `__('Contact me')`, never a dotted key. Every new key gets its Italian translation in `lang/it.json`. Use `:placeholder` parameters instead of string concatenation. Enum labels and Filament resource labels live in `lang/{en,it}/enums.php` and `resources.php`.

## Tracked elements

To track clicks, hovers or impressions on a new element:

1. Add `data-pan="<kind>-<area>-<identifier>"` to the element.
2. Add the same name to the whitelist in `AppServiceProvider::configurePan()`; unlisted names are dropped silently.
3. Check that the total stays within `maxAnalytics`.

Naming, whitelist and reading the data are described in [analytics/pan.md](analytics/pan.md).
