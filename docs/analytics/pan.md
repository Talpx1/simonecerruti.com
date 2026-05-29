# Pan — UI interaction counters

Pan (`panphp/pan`) is the second tracking layer. It answers a different question than `visit_sessions`:

> "Of the people who reached the home page, how many clicked the 'Contacts' nav link versus the 'Hire' floating CTA?"

It's an aggregate counter, not a session log. There is no per-user trail, no PII, no consent gate.

## How it works in one paragraph

The Pan JS library is injected into every page by Pan's `InjectJavascriptLibrary` middleware (added globally on package install). When the user **hovers**, **clicks**, or **sees in the viewport** (impression) any element with a `data-pan="<event-name>"` attribute, the JS posts to `/pan/events` and the server increments one of three counters (`hovers`, `clicks`, `impressions`) for that event name in the `pan_analytics` table.

That's the whole loop. Three counters per event name, nothing else.

## The data model

```
pan_analytics
─────────────
id           bigint, PK
name         string, unique-ish    ← the value of data-pan
impressions  bigint, default 0
hovers       bigint, default 0
clicks       bigint, default 0
```

One row per distinct event name. The schema is intentionally minimal: no timestamps, no user reference. Counters only.

## The whitelist

Without a whitelist, anyone can spam arbitrary event names by editing HTML in their browser. We configure Pan in `app/Providers/AppServiceProvider.php::configurePan()`:

```php
PanConfiguration::maxAnalytics(100);
PanConfiguration::allowedAnalytics([
    'cta-nav-home',
    'cta-nav-about',
    // … 21 entries total
]);
```

Pan ignores any event whose name isn't in the whitelist. New `data-pan` attributes must be added there too, or they're silently dropped.

`maxAnalytics(100)` caps the number of distinct rows. Past 100 Pan refuses to insert new ones, which prevents runaway table growth from a misconfigured whitelist.

## Event naming convention

Pattern: `<kind>-<area>-<identifier>`.

Examples:

| Name | Where the attribute lives | Reads as |
|---|---|---|
| `cta-nav-home` | menu link to home | "click on the home nav CTA" |
| `cta-nav-blog` | menu link to blog | … |
| `cta-social-instagram` | every `<a>` to instagram (3 social-links variants) | aggregate across all variants |
| `cta-hero-projects` | "See all" link in the home hero `projects` section | … |
| `cta-hero-contacts` | "Don't wait, let's talk" CTA in the home intro | … |
| `cta-contact-email` | mailto link in the floating contacts widget | … |
| `cta-contact-form` | "HIRE" link in the floating contacts widget | … |
| `card-project-click` | every project-card's "View" CTA | aggregate, not per-project |
| `card-blog-click` | every blog-article-card's "Read" CTA | aggregate, not per-article |
| `section-impression-hero` | home intro `<section>` | counted when the section enters the viewport |
| `section-impression-services` | home `how-i-work` `<section>` | … |
| `section-impression-projects` | home projects `<section>` | … |
| `section-impression-cta` | home about `<section>` | … |

Cards intentionally don't encode the specific project or article slug — that level of detail belongs in `page_views`. Pan is for *which CTA*, not *which content*.

## Adding a new tracked element

```
1. Pick a name following the kind-area-identifier convention.

2. Add it to the whitelist in AppServiceProvider::configurePan().

3. Add the attribute in the blade template:
       <a href="..." data-pan="<your-name>">…</a>

4. Open the page in a browser. Click / hover / scroll past it.

5. Confirm the row exists in /admin (PanEventsWidget at the bottom of
   the dashboard) or via:
       php artisan tinker --execute 'DB::table("pan_analytics")->get()->dd();'
```

If the row never shows up, you missed step 2.

## Why card-level events aren't per-slug

The plan considered `card-project-click-<slug>` but rejected it: every new project would need to be added to the whitelist, and the `pan_analytics` table would grow unboundedly. The `page_views` table already tells you which projects were viewed (the route is `project.show` with the slug in the URL). For the same insight at a finer grain, query `page_views` grouped by `route_name`.

## Consent — none required

Pan stores no identifiers. The counters are aggregate, the JS posts no cookies, no Pan row references a visitor. It runs regardless of cookie consent. The cookie policy lists Pan implicitly under "Strictly Necessary / aggregate analytics with no personal data".

## Reading the data

The `PanEventsWidget` on the Filament dashboard groups events by prefix (`cta-nav-*`, `cta-social-*`, `card-*`, `section-impression-*`, etc.) and renders a small table per group with impressions / hovers / clicks columns. That's the day-to-day view.

For ad-hoc analysis, query the table directly:

```sql
SELECT name, impressions, hovers, clicks,
       ROUND(clicks::numeric / NULLIF(impressions, 0) * 100, 1) AS click_rate_pct
FROM pan_analytics
ORDER BY clicks DESC;
```

## Flushing

CLI command bundled with the package:

```bash
php artisan pan:flush   # truncates pan_analytics
```

Useful if you change the whitelist semantics and want to start fresh. There is no Pan-side migration that handles renames.

## Pan vs. visit tracking — when to use which

| Question | Use |
|---|---|
| Where did this visitor come from? | `visit_sessions` |
| What pages did they read? | `page_views` |
| Which CTAs do people click on the home page? | `pan_analytics` |
| Are visitors actually scrolling past the hero? | `pan_analytics.section-impression-hero` |
| Which specific blog article is most read this month? | `page_views` filtered by `route_name = 'blog_article.show'` |
| What's the click-through rate of the floating "Hire" CTA? | `pan_analytics.cta-contact-form` clicks vs. impressions |
