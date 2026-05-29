# Visit tracking flow

Every GET request to a public page is intercepted by `TrackVisit` middleware. This document walks through what happens, in order.

## The pipeline

```
Request
  │
  ▼
┌─────────────────────────────────────────┐
│ Laravel middleware stack (web group)    │
│                                         │
│   EncryptCookies                        │
│   AddQueuedCookiesToResponse            │
│   StartSession                          │
│   ShareErrorsFromSession                │
│   VerifyCsrfToken                       │
│   SubstituteBindings                    │
│   CookieConsentMiddleware  ←─ reads cookie_consent, shares it with views
│   TrackVisit              ←─ the new middleware
└─────────────────────────────────────────┘
  │
  ▼
Controller / Livewire page renders
  │
  ▼
TrackVisit.handle() runs AFTER $next($request), so the response is final.
  │
  ▼
Response
```

`TrackVisit` is intentionally the innermost web middleware. Its `handle()` runs after the response is built (so it can read `view()->shared('cookieConsent')` set by `CookieConsentMiddleware`, and call `Cookie::queue()` before `AddQueuedCookiesToResponse` writes Set-Cookie headers on the way out). Then the **DB writes are deferred to `terminate()`** — Laravel calls `terminate()` after the response is sent to the client, so the visitor doesn't wait for `INSERT visit_sessions` and `INSERT page_views`. State is carried from `handle()` to `terminate()` via a private property on the middleware instance.

Consequences:

- a thrown exception inside the DB phase can no longer affect the page response — the client already has the response. Failures only show up in logs.
- queued cookies still reach the client because they're queued in `handle()`, *before* the framework writes Set-Cookie headers.
- `Cookie::queue()` **cannot** be used in `terminate()` — the response is already out. That's why session/visitor IDs are generated up-front in `handle()` and inserted into the DB later.

## Skip rules — when nothing is written

`TrackVisit::shouldTrack()` returns `false` (and persists nothing) for:

- Any **non-GET** request — POST/PUT/DELETE/etc. never count as page views.
- Any response with a **redirect status** (3xx). This avoids double-tracking `localizationRedirect`'s 302 from `/contacts` → `/it/contacts`. Only the second, real GET makes it into the DB.
- Any path matched by `config('analytics.skip_paths')`. Current list:
  - `admin`, `admin/*` (Filament panel)
  - `_debugbar*`, `telescope*` (dev tools)
  - `livewire/*` (Livewire AJAX endpoint)
  - `up` (health check)
  - `build/*`, `storage/*` (static assets)
  - `sitemap*.xml`, `robots.txt` (crawlers' technical resources)

Important consequence: **Livewire `wire:navigate` is tracked**. It's a real GET to the destination page; only the `/livewire/update` AJAX bus is skipped.

## What gets written

If `shouldTrack()` passes, two writes happen:

### 1. Session row — only on first hit (or after the 30-min window expires)

```php
VisitSession::query()->create([
    'visitor_id'         => …,  // null without consent, UUID with consent
    'source'             => 'instagram',
    'medium'             => 'social',
    'campaign_id'        => 42,    // null if no UTM match
    'utm_source'         => 'instagram',
    'utm_medium'         => 'social',
    'utm_campaign'       => 'ig-launch-apr',
    'utm_term'           => null,
    'utm_content'        => null,
    'referrer_url'       => 'https://www.instagram.com/...',
    'referrer_host'      => 'www.instagram.com',
    'landing_path'       => '/it/contatti',
    'landing_route_name' => 'contacts',
    'locale'             => 'it',
    'ip'                 => …,  // null without consent
    'user_agent'         => …,  // null without consent
    'device_type'        => …,  // mobile/tablet/desktop/bot/unknown — null without consent
    'country'            => 'IT',  // from CF-IPCountry header — always set, server-side data
    'consent_analytics'  => true,
    'started_at'         => now(),
    'last_activity_at'   => now(),
    'pageview_count'     => 1,
]);
```

### 2. Page view row — on every tracked hit

```php
PageView::query()->create([
    'visit_session_id' => $session->id,
    'url_path'         => '/it/contatti',
    'route_name'       => 'contacts',
    'locale'           => 'it',
    'viewed_at'        => now(),
]);
```

## The 30-minute sliding window

A `vs_id` cookie (necessary, expiring after 30 minutes of inactivity) holds the UUID of the active session. Each tracked request:

- looks up the session by id and checks `last_activity_at >= now() - 30min`
- on hit: increments `pageview_count`, updates `last_activity_at`, inserts a new `page_views` row
- on miss (expired or no cookie): treats the visit as new, runs `DetectVisitSource` and creates a fresh session

Window size lives in `config('analytics.session_window_minutes')`.

## Source detection (`DetectVisitSource`)

Lives in `app/Actions/Analytics/DetectVisitSource.php` — a single-method action (`lorisleiva/laravel-actions`) that takes scalars (no `Request`, so it's testable in isolation) and returns a `VisitSourceData` DTO. It hits the DB to look up campaigns, which is why it's an Action rather than a pure Support utility. Order of decisions:

1. **`utm_campaign` present?**
   - Look up `Campaign::active()->where('slug', utm_campaign)` (the `active()` scope checks the `starts_at` / `ends_at` window).
   - If found → use `campaign.source` and `campaign.medium`, set `campaign_id`.
   - If not found → fall back to raw `utm_source` (or `VisitSourceType::UNKNOWN` when even that is missing), keep `campaign_id = null` but **always persist the raw UTMs**.

2. **Referrer URL present?** Parse the host:
   - Same host as current request → `source = utm_source ?? internal`, `medium = utm_medium`. (Cross-page navigation just creates a new pageview row on the same session.)
   - Host in `config('analytics.social_hosts')` (e.g. `www.instagram.com → instagram`) → `source = instagram` (the matched value), `medium = social`.
   - Host matches a prefix in `config('analytics.search_hosts')` (e.g. `google.` → google) → `source = google`, `medium = organic`.
   - Otherwise → `source = referrer_host`, `medium = referral`.

3. **Nothing** → `source = direct`.

`source` is a `string` column on purpose (not casted to the enum) because the campaign + referrer paths supply specific values like `instagram`, `qr`, `google`, or any host. The `VisitSourceType` enum only models the three fallback buckets (`direct`, `internal`, `unknown`) and drives Filament badge colours / filter options for those, not column validation. `medium` is a string column but conventionally holds one of the `VisitMediumType` enum values (the form's `Select` enforces this on writes; legacy free-text reads stay tolerated).

## Consent gating — what's collected when

| Field | No consent | Analytics consent given |
|---|---|---|
| `vs_id` cookie | ✅ set (necessary, 30 min, anonymous session id) | ✅ |
| `v_id` cookie | ❌ not set | ✅ set (analytics, 1 year, returning visitor) |
| `visitor_id` column | `null` | UUID (matches `v_id` cookie) |
| `ip` column | `null` | the client IP (real one, behind Cloudflare via `CF-Connecting-IP` / trustProxies) |
| `user_agent` column | `null` | the raw UA string |
| `device_type` column | `null` | derived from UA (mobile/tablet/desktop/bot/unknown) |
| `country` column | ✅ from `CF-IPCountry` header | ✅ — server-side geo from Cloudflare, never PII |
| `source`, `medium`, UTMs, `referrer_*`, `landing_*`, `locale` | ✅ always | ✅ |

The split is deliberate: aggregate "how many visits / from where" is necessary for the business and doesn't need consent. The PII-shaped fields (IP, UA, returning-visitor identifier) need explicit analytics consent.

## Cookies set by this system

| Cookie | TTL | Category | Purpose |
|---|---|---|---|
| `vs_id` | 30 min (sliding) | Strictly necessary | Anonymous session identifier — without it the 30-min window can't work |
| `v_id` | 1 year | Analytics | Identifies a returning visitor across multiple sessions — only set after analytics consent is granted |

Both are first-party, httpOnly, `SameSite=Lax`, encrypted by Laravel. Cookie policy page (`/cookie-policy`) lists them publicly.

## Why two tables, not one

`visit_sessions` answers *"who came, from where, when"*. `page_views` answers *"what they read"*. Splitting lets you:

- count sessions per source without `DISTINCT visitor` gymnastics
- compute pages-per-session as `AVG(pageview_count)`
- query top pages independently of which session led there
- delete a session and cascade-delete its page views in one foreign-key constraint
