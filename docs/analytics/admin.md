# Admin reference

This is the read-side: what each piece of the Filament admin shows, and how to read it.

## `/admin` — the dashboard

Seven widgets, in this stacking order:

### 1. `AnalyticsStatsOverview`

Four stat cards:

- **Visits today** — count of `visit_sessions.started_at` falling within the current calendar day.
- **Visits last 7 days** — rolling, not calendar-week.
- **Visits last 30 days** — rolling.
- **Top source (30d)** — the value of `source` with the highest count over the rolling 30-day window. Shows `-` if there are no sessions yet.

Polls every 60 seconds.

### 2. `VisitsByDayChart`

Line chart, 30 buckets (one per day) ending today. Each bucket counts `visit_sessions.started_at` falling on that day. Polls every 60 seconds.

Reading it: trend, not absolutes. A spike on a single day usually means a campaign launch; a slow ramp usually means organic growth or a long-tail campaign (e.g. QR codes).

### 3. `VisitsBySourcePieChart`

Doughnut chart grouped by `visit_sessions.source` for the rolling 30-day window. Labels come from the `VisitSourceType` enum's localized labels when the value matches a case; otherwise the raw `source` string is shown.

Reading it: the slice for `direct` is usually large for a personal site — bookmarks, retyped URLs, and visits with no referrer all land there. `internal` is suppressed by the source-detection logic (only first-hit sources are recorded; internal navigation creates additional page views, not new sessions).

### 4. `TopReferrersTable`

Top 10 `(referrer_host, source)` pairs over the rolling 30-day window, **excluding** `internal` and `direct` source values and `null` referrer hosts. Aggregated count of sessions.

Reading it: rows here are exclusively external referrals that don't carry a `utm_campaign`. Visits with a matched campaign show up under "Top campaigns" instead, even if they came from a known social referrer.

### 5. `TopPagesTable`

Top 10 `(url_path, route_name)` pairs in `page_views` over the rolling 30-day window. Counts all page views, not unique sessions — re-reads count too.

Reading it: this is the only widget that shows **content** popularity. The home page (`/`, `/it`, `/en`) usually dominates; what's interesting is what ranks below it.

### 6. `TopCampaignsTable`

Top 10 **active** campaigns by sessions in the last 30 days (active = inside the `starts_at` / `ends_at` window). Campaigns whose window has ended are excluded even if they have recent sessions.

Reading it: the `Visits` column is "sessions in the last 30 days bound to this campaign", not lifetime sessions. A campaign you launched 60 days ago will read 0 here, even if it sent traffic at launch.

### 7. `PanEventsWidget`

Groups all rows in `pan_analytics` by prefix (`cta-nav-*`, `cta-social-*`, etc.) and renders one small table per group with `impressions`, `hovers`, `clicks` columns. Read [`pan.md`](pan.md) for the details.

Reading it: low click counts on a high-impressions section usually mean the section's CTA isn't pulling. High clicks on `card-project-click` plus low clicks on the project's actual external-link buttons (not tracked currently) would suggest visitors are reaching project pages but not converting.

---

## `/admin/campaigns` — Campaign CRUD

Standard Filament CRUD. The list view supports:

- a computed `Status` badge (`scheduled` / `active` / `ended`) derived from `starts_at` / `ends_at`
- inline **End now** action (sets `ends_at = now()`) — only visible for campaigns whose window hasn't ended
- search by name and slug
- filter by source (dynamic — only the source values currently in the table appear in the filter dropdown), medium, tags, and an "Active only" toggle that applies the `Campaign::active()` scope
- per-row count of `Visits` (lifetime, not 30-day — different from the dashboard widget)

The form does what [`campaigns.md`](campaigns.md) describes: name with auto-slug-fill, slug uniqueness validation, required source with datalist autocomplete, optional medium picked from `VisitMediumType`, optional `starts_at` / `ends_at`, free-form Spatie tags, plus description / notes. On Edit, the **Outbound URL** section at the top of the form shows the ready-to-publish URL with a copy button.

---

## `/admin/visit-sessions` — Visit sessions (read-only)

This resource is **read-only by design**: `canCreate`, `canEdit`, `canDelete` all return `false`. Sessions are produced by the middleware; manual editing would corrupt the history.

### List page

Default sort: `started_at desc` (newest first).

Columns (all toggleable):

- `started_at` — when the session began
- `source` — badge, colored via `VisitSourceType` enum for fallback values (`direct`/`internal`/`unknown`), gray otherwise
- `medium` — badge, colored via `VisitMediumType` enum when matched
- `campaign.name` — from the relationship; empty if `campaign_id IS NULL`
- `utm_campaign` — raw value; toggled off by default (turn it on when investigating typos)
- `referrer_host`
- `landing_path` — truncated to 40 chars, tooltip shows the full value
- `pageview_count` — number of `page_views` rows tied to this session, sortable
- `country` — ISO 2-letter code from `CF-IPCountry`
- `locale`

Filters (combine freely):

- **date range** — two `DatePicker` widgets, `from` and `until`, applied as `started_at BETWEEN` when both are set, or as a single-sided bound when only one is
- **source** — select from the `VisitSourceType` enum values (fallback buckets only — see [campaigns.md](campaigns.md) for why)
- **medium** — select from the `VisitMediumType` enum values
- **campaign** — select from the relationship (lists all campaigns, not just active ones)
- **campaign tags** — multi-select of `campaign_tag` Spatie tags; matches any session whose campaign carries one of the picked tags
- **locale** — `it` / `en`
- **analytics consent** — ternary (any / consent given / consent not given)

### View page

Click a row → opens the read-only view. Two parts:

1. **Infolist** with five sections:
   - **Overview** — started_at / last_activity_at / source / medium / campaign / pageview_count / locale / country
   - **Landing** — landing_path (copyable) / landing_route_name
   - **Referrer** — referrer_url (full URL) / referrer_host
   - **UTM** — utm_source / utm_medium / utm_campaign / utm_term / utm_content (all five always shown, `-` for missing values)
   - **Visitor** — analytics_consent (icon), visitor_id, ip, device_type, user_agent (full string, can be long)

2. **Page views relation manager** — every `page_views` row tied to this session, default-sorted `viewed_at desc`. Shows: viewed_at / url_path / route_name / locale. No actions, read-only.

If you need a session's full trail in chronological order (oldest first), sort the page views ascending in the relation manager header. This is the closest the system gets to a "session recording" for non-analytics-consented visitors — just the sequence of pages.

---

## What the admin can't tell you

A few intentional limits worth knowing:

- **No per-visitor timeline.** `visitor_id` is captured (with consent) but there's no UI grouping sessions by it. Query the DB directly if you need this — see the SQL example at the end of [`examples.md`](examples.md) Step 5.
- **No funnel / conversion reports.** Determine your own funnel by joining `visit_sessions` to `page_views` and querying for sessions that touched both source and destination routes.
- **No A/B comparison view.** Two campaigns with different slugs but the same URL path can be compared by side-by-side filter views on the visit-sessions list. There's no automated diff.
- **No real-time view.** Widgets poll every 60s; the dashboard isn't a live stream.
- **Pan and visit tracking don't cross-reference.** A click on `cta-nav-blog` is incremented in `pan_analytics` and the resulting `/blog` page view is recorded in `page_views`, but they're not linked. They answer different questions.
