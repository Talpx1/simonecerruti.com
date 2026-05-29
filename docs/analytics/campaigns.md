# Campaigns

A **campaign** is a row in the `campaigns` table that you create from `/admin/campaigns`. It does one job: when a visitor arrives with `?utm_campaign=<slug>` in the URL, the middleware binds that visit to the matching campaign — provided the campaign is currently *active* (see below) and the slug matches exactly.

## The contract

```
Outbound URL:   https://simonecerruti.com/?utm_source=X&utm_medium=Y&utm_campaign=<slug>
                                                                                   │
                                                                                   ▼
                                                          campaigns.slug must equal this
```

A campaign is **active** when `starts_at IS NULL OR starts_at <= now()` AND `ends_at IS NULL OR ends_at >= now()`. Both timestamps are independent: leave `starts_at` null to start immediately, leave `ends_at` null for no expiry. Set `ends_at = now()` to deactivate immediately.

If a row exists with that `slug` AND is currently active:

- `visit_sessions.campaign_id` is set to the campaign's id
- `visit_sessions.source` is set to the campaign's `source` (overriding `utm_source`)
- `visit_sessions.medium` is set to the campaign's `medium` (overriding `utm_medium`)
- All raw `utm_*` values are still persisted in their own columns

If no row matches (typo in the slug, campaign window has ended or not started yet, or it doesn't exist):

- `visit_sessions.campaign_id` is `null`
- `visit_sessions.source` falls back to the raw `utm_source` value (or `unknown` if `utm_source` is also missing)
- Raw UTMs are still persisted, so you can still spot the visit in the admin under "utm_campaign" — you just won't have campaign-level grouping or naming

The fallback behaviour is intentional. A typo in a flyer should not silently drop traffic.

## The fields on a Campaign

| Field | Type | Why it exists |
|---|---|---|
| `name` | string | Human label shown in the admin and widget tables. **This is what your future self reads at a glance** — make it descriptive. |
| `slug` | string, unique | The string that must equal `utm_campaign`. Kebab-case, ASCII-safe, no surprises. |
| `source` | string, required | What `visit_sessions.source` becomes for matched visits. Drives charts, top-source stats, and the source filter. |
| `medium` | enum `VisitMediumType`, nullable | Channel qualifier from a closed set: `social`, `email`, `organic`, `paid`, `display`, `referral`, `affiliate`, `print`, `physical`. Strict enum because the medium taxonomy must stay consistent across campaigns. |
| `description` | text, nullable | Internal note about *what* the campaign is. |
| `starts_at` | timestamp, nullable | When the campaign becomes eligible to match. Null means "from now". |
| `ends_at` | timestamp, nullable | When the campaign stops matching. Null means "indefinitely". Set to `now()` for an immediate kill-switch. |
| `tags` | Spatie polymorphic tags | Free-form grouping across campaigns: by material (`material-business-card`, `material-stickers`), batch (`batch-2026-spring`), or anything else you want to filter on in the admin. |
| `notes` | text, nullable | Anything else — budget, internal stakeholders, A/B variant rationale, etc. |

Choose `source` and `medium` once, when you create the campaign. Don't tune them after the fact — historical visits keep the old values frozen on `visit_sessions`, but the column on the campaign row is what new visits inherit.

## Choosing a good slug

The slug is the only field shared with the outside world. Once you publish a URL containing it, **you can't change the slug without breaking the binding for everyone who clicks that URL later**.

Recommendations:

- Kebab-case. ASCII letters, digits, hyphens.
- Encode the channel + intent, not the date alone. `ig-launch` is more meaningful than `apr-2026`. If you reuse channels for repeated campaigns, suffix with a stable identifier — `ig-launch-apr26` over `instagram-1`.
- Keep it short. People may type these into terminals when debugging.
- Avoid reusing slugs across campaigns. Once "burned", create a new one. Deactivate the old one if it should no longer bind.

## Source vs. medium — the convention

- **`source`** = where the visitor was, immediately before clicking. Free-text string. Examples: `instagram`, `linkedin`, `newsletter`, `qr`, `business-card`, `print-flyer`. Pick anything that names the specific origin.
- **`medium`** = the *kind* of channel. Pulled from the `VisitMediumType` enum — pick one of `social`, `email`, `organic`, `paid`, `display`, `referral`, `affiliate`, `print`, `physical`.

Mirror this to UTMs: `utm_source` ≈ `source`, `utm_medium` ≈ `medium`. The values you put in the URL are persisted raw even when a campaign overrides them, so consistency makes the admin filters useful.

## Variants of the same campaign

You have two complementary ways to group related campaigns or distinguish variants:

- **`utm_content` for in-campaign variants.** If a single campaign has multiple creatives or placements (e.g. a `card-qr-2026` campaign used at two different events), keep one `Campaign` row and differentiate via `?utm_content=event-a` vs `?utm_content=event-b`. The middleware persists `utm_content` raw on `visit_sessions`, so you can filter sessions by it in the admin. No schema change needed.
- **Tags for cross-campaign grouping.** If you have *separate* campaigns that share a property — `business-card` vs `stickers` as physical materials, or three variants of `card-qr-2026` printed in different batches — add tags to each campaign (`material-business-card`, `batch-spring-2026`, …). The Sessions table has a "Campaign tags" filter that resolves through the campaign relationship, so filtering by tag gives you every session whose campaign carries it.

Tag naming convention: prefix with a dimension (`material-`, `batch-`, `event-`, `year-`) so the filter list stays scannable.

## Creating a campaign — admin walkthrough

1. Go to `/admin/campaigns`.
2. Click **Create** (top right).
3. Fill in `name`. The slug field auto-fills from the name (kebab-cased) — adjust it if you want a shorter or more descriptive slug. **Decide the slug here, you can't change it later without consequences.**
4. Fill in `source` (required) and pick a `medium` (optional).
5. Optionally set `starts_at` / `ends_at` for scheduling.
6. Add tags for cross-campaign grouping.
7. Add a `description` and `notes` for your future self.
8. Save.

After save, open the campaign and use the **Outbound URL** field at the top of the form to copy the ready-to-publish URL.

The campaign now exists and will match any visit arriving with `?utm_campaign=<that-slug>` during its active window.

## Ending

When the campaign ends:

- Set `ends_at` to the desired end datetime (past datetime for "right now"), or use the inline **End now** action on the campaigns table to set `ends_at = now()` in one click.
- Existing visits in `visit_sessions` keep their `campaign_id` and their captured source/medium — historical data is not rewritten.
- New visits arriving with that `utm_campaign` after `ends_at` will fall through to the raw-UTM path (no `campaign_id` binding).

Don't delete the campaign row unless you actually mean to break the historical link. Deleting cascades to `visit_sessions.campaign_id = NULL` (nullOnDelete), so charts that joined on campaigns will lose context.

## The URL format

```
https://simonecerruti.com/[locale/][path]?utm_source=<value>&utm_medium=<value>&utm_campaign=<slug>[&utm_term=<value>&utm_content=<value>]
```

- `utm_source` — required by convention, even though the middleware tolerates missing values.
- `utm_medium` — required by convention.
- `utm_campaign` — required for the binding to happen. Must equal `campaigns.slug` exactly (case-sensitive).
- `utm_term` — optional. Persisted raw. Typically the search keyword for paid search.
- `utm_content` — optional. Persisted raw. Typically used to A/B test creative variants of the same campaign (e.g. `utm_content=variant-a`).

The landing path doesn't matter for matching — you can attach UTMs to any URL on the site, the visitor just lands there and the middleware picks them up.

## Quick checks

After publishing a campaign URL, confirm it works:

1. Open the URL in a private window.
2. Refresh `/admin/visit-sessions`. The newest row should:
   - have your campaign in the `Campaign` column
   - show `source` matching the campaign's `source` and `medium` showing the enum label (badge colour follows the `VisitMediumType` enum; `VisitSourceType` colours apply only to fallback values: `direct`, `internal`, `unknown`)
   - have the full `utm_campaign=...` query in the raw `utm_campaign` column (toggle the column on if hidden)
3. Open the session's view page. The **UTM** section in the infolist shows all five raw `utm_*` values.

If the campaign column is empty but the raw UTM is captured: the slug doesn't match exactly, or the campaign's active window doesn't include "now". Check `starts_at` / `ends_at` in `/admin/campaigns`.
