# Internal analytics

Three independent systems coexist on this site:

| System | What it tracks | Where data lands | Consent gate |
|---|---|---|---|
| **Microsoft Clarity** | Heatmaps, session recordings, mouse trails | clarity.microsoft.com (external) | analytics |
| **Visit tracking** (this doc) | Server-side sessions, page views, source/campaign attribution | Postgres tables `visit_sessions` + `page_views` | partial (see below) |
| **Pan** (this doc) | Click / impression counters on specific UI elements (`data-pan="..."`) | Postgres table `pan_analytics` | none (aggregated, no PII) |

This documentation covers the last two. They were built to answer a single business question that Clarity cannot: **where do my visitors come from, and which campaigns actually move people to the site?**

## Read these in order

1. **[Flow](flow.md)** — request path through the middleware, what gets persisted, and how consent gates which fields.
2. **[Campaigns](campaigns.md)** — how to create a campaign in the admin and wire it to outbound URLs (Instagram, QR, newsletter…).
3. **[Pan events](pan.md)** — how `data-pan` attributes turn into rows in `pan_analytics` and how to whitelist new events.
4. **[Examples](examples.md)** — two end-to-end walkthroughs: a sponsored Instagram post and the QR code on the business card.
5. **[Admin reference](admin.md)** — what each Filament resource and widget shows, and how to read it.

## TL;DR for a recurring task

You launched a new outbound link (a sponsored post, a QR on a flyer, a newsletter blast). To make it appear in the admin:

```
1. /admin/campaigns → create campaign
   - name:       "Instagram Launch – April"
   - slug:       "ig-launch-apr"        ← this is what utm_campaign must equal
   - source:     "instagram"            ← inherited by all matched visits
   - medium:     social                 ← from VisitMediumType enum
   - starts_at:  (leave empty → starts now)
   - ends_at:    (leave empty → indefinitely; set to a date to schedule the end)

2. Copy the outbound URL from the form's "Outbound URL" field, or build it yourself:
   ?utm_source=instagram&utm_medium=social&utm_campaign=ig-launch-apr

3. Done. First visit creates a row in visit_sessions with the campaign_id bound.
```

See [examples.md](examples.md) for the full Instagram and QR walkthroughs.

## Where things live in the codebase

- `app/Http/Middleware/TrackVisit.php` — the only place where rows are written to `visit_sessions` / `page_views`.
- `app/Actions/Analytics/DetectVisitSource.php` — DB-backed detector that turns UTM + referrer + current host into a `VisitSourceData` DTO (looks up campaigns by slug).
- `app/Support/Analytics/DeviceTypeDetector.php` — pure parser, `string → DeviceType`.
- `app/Enums/VisitSourceType.php` — fallback source buckets when we can't identify the specific origin: `direct`, `internal`, `unknown`.
- `app/Enums/VisitMediumType.php` — closed enum of mediums: `social`, `email`, `organic`, `paid`, `display`, `referral`, `affiliate`, `print`, `physical`.
- `app/Enums/DeviceType.php` — `mobile`, `tablet`, `desktop`, `bot`, `unknown`.
- `app/Models/{Campaign, VisitSession, PageView}.php` — the Eloquent models.
- `app/Filament/Resources/Campaigns/` — admin CRUD for campaigns.
- `app/Filament/Resources/VisitSessions/` — read-only browse + drill-down for sessions.
- `app/Filament/Widgets/` — six dashboard widgets (stats, charts, top tables) + the Pan summary widget.
- `app/Providers/AppServiceProvider.php` (`configurePan`) — the Pan allowed-events whitelist.
- `config/analytics.php` — session window, cookie names, skip paths, social/search host maps.
- `resources/views/components/social-links.blade.php`, `resources/views/layouts/public/menu.blade.php`, etc. — the `data-pan` attribute insertion points.
