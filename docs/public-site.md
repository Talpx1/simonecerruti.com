# Public site

How the public pages are built: the frontend runtime, the data behind the back-office, translations and tracked elements.

## Frontend runtime

`resources/js/public.js` is the single entry point for every public page. It exposes `window.SC` with shared helpers (`reduce`, `fine`, `clamp`, `lerp`, `smooth`, `$`, `$$`, `mouse`) and `SC.onFrame`, an array of callbacks run on each animation frame as `fn(now, dt)`.

Two flags drive every effect: `SC.reduce` (`prefers-reduced-motion`) and `SC.fine` (a precise pointer). Effects that need the mouse or heavy motion must check them.

### Page lifecycle

The site uses `wire:navigate`, so the document is never reloaded between pages.

- `livewire:navigating` calls `resetPage()`: it runs every registered cleanup, empties `SC.onFrame` and resets the per-page state.
- `livewire:navigated` calls `refresh()`: it rebinds the menu, copy buttons, reveals, word scrubs, the logo and every `data-fx` module. `refresh()` skips work if `<main>` is the same element as the last run.

Anything attached to `document`, `window` or an observer must be removed in a cleanup, or it will leak across navigations.

### `data-fx` modules

A `data-fx="name"` attribute on an element loads `resources/js/pages/name.js` through `import.meta.glob`, so each module is its own chunk and only loads on pages that use it.

To add one:

1. Create `resources/js/pages/<name>.js` with a default export `function (el, SC)`.
2. Put `data-fx="<name>"` on the root element in Blade.
3. Return a cleanup function. It runs on the next `wire:navigate` and must undo listeners, observers and timers. Push per-frame work onto `SC.onFrame` (it is cleared automatically).

```js
export default function (el, SC) {
    const rows = SC.$$('.xrow', el);
    const offs = rows.map((row) => {
        const toggle = () => row.classList.toggle('open');
        row.addEventListener('click', toggle);
        return () => row.removeEventListener('click', toggle);
    });

    return () => offs.forEach((off) => off());
}
```

The module is skipped if `el` is no longer connected when it finishes loading. `faq.js` is the smallest reference.

### Reveals and other markup hooks

- `.rv`: the element slides up and fades in the first time it enters the viewport (an `IntersectionObserver` adds `.in`). With reduced motion it is shown immediately.
- `[data-split]`: text is split into letters that react to the cursor (variable font weight).
- `.scrub p`: words light up as the paragraph scrolls; `data-acc` is a regex marking accent words.
- `.mag`: magnetic hover. `[data-cursor]`: label shown inside the custom cursor.
- `[data-copy]`: copies the value to the clipboard (`data-copied` is the confirmation label).

### Persisted layers

The cursor, grain, vignette and progress bar live in `#fx-layers`, wrapped in `@persist('fx-layers')` in `layouts/public/index.blade.php`. Livewire keeps them across navigations, so they must not be re-created by modules. The `requestAnimationFrame` loop in `public.js` drives them and is started once.

### Home canvas and three.js

Only the home page owns the `#gl` canvas (`pages/home/home.blade.php`). `three` is imported dynamically from `pages/home.js`, so no other page downloads it. If the import or WebGL initialisation fails, the `no-gl` class is set on the root and the page falls back to its static look. Do not add a second canvas or a static `three` import elsewhere.

## CSS organisation

`resources/css/app.css` is the only stylesheet entry. It imports Tailwind, the Filament styles, then `site.css`, `cookie.css` and `pages/*.css`, and defines the theme tokens (`--color-*`, `--font-*`) as Tailwind theme variables.

- `site.css`: design tokens on `:root` (`--bg`, `--ink`, `--mute`, `--acc`, `--gut`, ...), fixed layers, navigation, buttons and shared components.
- `pages/<page>.css`: styles scoped to a single page or feature (`home`, `blog`, `contacts`, `legal`, `cookie-consent`). A new page style goes in its own file, imported at the end of `app.css`.
- Use the tokens instead of literal colours.

Fonts are self-hosted: Archivo Variable comes from `@fontsource-variable/archivo` (imported in `app.css`, `wdth` axis included), so no request goes to Google Fonts.

## Back-office data model

### Client

`clients` table, `Client` model: `name` and a translatable `description`. A client has many projects (`projects.client_id`). Managed in the Filament Client resource. Deleting goes through the `DeleteClient` action, which first nulls `client_id` on the client's projects so no project is lost.

### Project

On top of the usual fields, the Project form has the case-study fields shown on the project page:

| Field | Use |
|---|---|
| `client_id` | Relation to `Client` (replaces the old free-text client name; a migration moved the existing names into `clients`). |
| `starting_point`, `goal`, `outcome` | The three narrative blocks of the case study. |
| `kind` | Short label ("Management software"). If empty, `kind_or_tag_names` falls back to the project's tag names joined with ` + `. |
| `features` | Repeater of `{title, description}`. |
| `home_summary` | Short text for the home page. |

Related actions: `GetAdjacentProjects` (previous/next navigation) and `GetServiceAreaProjects`.

### Tag

Tags have a `description` and an optional `service`, a `ServiceAreas` case (`management_erp_crm`, `web_ecommerce_platforms`, `consulting_and_seo`). `GetServiceAreaProjects::run($area, $limit)` returns the featured projects that carry a tag with that service; the matching service landing page (`$area->getRouteName()`) uses it for its project section. To show a project on a landing page, tag it with a tag whose `service` is set.

### Blog

- `BlogSettings` (Filament settings page, group `blog`) holds the per-locale `author_bio`, shown in the article footer through `getAuthorBioForCurrentLocale()`; it is null when empty.
- The list is paginated (`BlogArticleList::ARTICLES_PER_PAGE`, 6).
- `GetFeaturedBlogArticles` returns the two latest published articles flagged `featured` that have a translation for the current locale.
- `BuildTableOfContents` adds ids to the `h2`/`h3` of the article HTML and returns the table of contents; `CalculateReadingTime` gives the reading time.
- The rich editor has a `Gallery` block (`app/Filament/RichContentBlocks/GalleryBlock.php`): 2 to 12 images with optional captions, stored in `blog/gallery` on the public disk. The preview view is `rich-content.blocks.gallery-preview`.

## Translations

The English phrase is the translation key: `__('Contact me')`, never a dotted key. Every new key gets its Italian translation in `lang/it.json`. Use `:placeholder` parameters instead of string concatenation. Enum labels and Filament resource labels live in `lang/{en,it}/enums.php` and `resources.php`.

## Tracked elements

To track clicks, hovers or impressions on a new element:

1. Add `data-pan="<kind>-<area>-<identifier>"` to the element.
2. Add the same name to the whitelist in `AppServiceProvider::configurePan()`; unlisted names are dropped silently.
3. Check that the total stays within `maxAnalytics`.

Naming, whitelist and reading the data are described in [analytics/pan.md](analytics/pan.md).
