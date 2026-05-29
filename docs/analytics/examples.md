# End-to-end examples

Two real scenarios. Read them in full; the second reuses concepts from the first.

---

## Example 1 — Sponsored Instagram post

You're launching a paid Instagram post promoting the **Projects** page. You want to know:

- how many people clicked through
- how many of them then visited the **Contacts** page from the projects page
- whether the post drove any returning visitors over the following weeks

### Step 1 — Create the campaign

Go to `/admin/campaigns` → **Create**.

| Field | Value |
|---|---|
| name | `Instagram Sponsored — Projects April 2026` |
| slug | `ig-sponsored-projects-apr26` |
| source | `instagram` |
| medium | `social` |
| description | `Paid promotion of the Projects page targeting design-curious EU audience` |
| starts_at | (leave empty → starts now) |
| ends_at | `2026-04-30 23:59` |
| tags | `material-paid-social`, `batch-spring-2026` |
| notes | `Budget €150 over 7 days. Ad set "Projects-EU-25-45".` |

Save.

### Step 2 — Build the destination URL

The post links to `/projects`. Append the standard UTMs:

```
https://simonecerruti.com/projects?utm_source=instagram&utm_medium=social&utm_campaign=ig-sponsored-projects-apr26
```

Use this exact URL in the Instagram Ads Manager **website URL** field. Don't shorten it through bit.ly or similar — that often strips UTMs.

> ⚠ Instagram occasionally rewrites outbound URLs to go through `l.instagram.com`. The `referrer` header on the landing request will then be `https://l.instagram.com/...`, which the middleware **does not** auto-classify as Instagram — but it doesn't matter, because `utm_campaign` already binds the visit to the right campaign and overrides `source`/`medium`.

### Step 3 — Verify in a private window

Before going live to ad spend, click the URL in a private window. Then `/admin/visit-sessions`:

- newest row's **Campaign** column = `Instagram Sponsored — Projects April 2026` ✓
- **Source** badge = `instagram`, **Medium** badge = `social` (pink, from `VisitMediumType::SOCIAL`) ✓
- **Landing** = `/projects` ✓
- Toggle on the hidden `utm_campaign` column → value = `ig-sponsored-projects-apr26` ✓

If any of those is wrong, fix the URL or the campaign row before pushing ad spend.

### Step 4 — Push the ad live, then read the data

After a few days, on the Filament dashboard:

- **AnalyticsStatsOverview** — "Top source (30d)" should reflect `instagram` if the campaign is driving meaningful share.
- **VisitsByDayChart** — spikes on days the ad ran.
- **VisitsBySourcePieChart** — Instagram slice visible.
- **TopCampaignsTable** — your campaign row with its 30-day visit count.

Drill-down query (admin URL: `/admin/visit-sessions`):

- Filter by **Campaign** = your campaign.
- Inspect each session. The infolist shows the full UTM block, the country, the device type, and a relation manager with **every page the visitor viewed in that session**.

Want pages-per-session for this campaign? Open the session view: the **Page views** relation manager lists them in order. Across all sessions, the campaign's `pageview_count` column on the list table is the per-session count.

### Step 5 — Returning visitors

If a visitor accepted analytics consent on their first visit, they also got a `v_id` cookie (1-year persistent). When they come back days later, the new session row carries the same `visitor_id`. Query:

```sql
SELECT visitor_id, COUNT(*) AS sessions
FROM visit_sessions
WHERE campaign_id = (SELECT id FROM campaigns WHERE slug = 'ig-sponsored-projects-apr26')
  AND visitor_id IS NOT NULL
GROUP BY visitor_id
HAVING COUNT(*) > 1
ORDER BY sessions DESC;
```

That tells you who came back. Returning visitors driven by a paid Instagram campaign are a strong leading indicator the creative worked.

### Step 6 — End of campaign

Either the `ends_at` you set up-front expires naturally, or use the inline **End now** action on the campaigns list to set `ends_at = now()`. The campaign keeps appearing in widgets for as long as it has visits in the last 30 days. Don't delete it — past sessions reference it via `campaign_id`.

If three months later someone clicks an old reshared Instagram link with that UTM, the visit still lands and is recorded — but `campaign_id` will be `null` because the campaign is past its `ends_at`. The raw `utm_campaign` value is still stored, so you can still find the visit by searching that column.

---

## Example 2 — QR code on business cards

You print 500 business cards. The back has a QR code that should take people to your home page. You want to know if the QR is actually getting scanned and from which kind of audience.

### Step 1 — Create the campaign

`/admin/campaigns` → **Create**:

| Field | Value |
|---|---|
| name | `Business card QR — 2026 batch` |
| slug | `card-qr-2026` |
| source | `qr` |
| medium | `print` |
| description | `Back-of-card QR code, batch printed for events in 2026` |
| starts_at | (leave empty) |
| ends_at | (leave empty — print runs are open-ended) |
| tags | `material-business-card`, `batch-2026` |
| notes | `500 cards printed by [printer name] in 2026-03. Reorder via [link]. Don't change slug across reprints — print runs reference it.` |

Note: `source = qr` is a free string. The `VisitSourceType` enum only contains fallback values (`direct` / `internal` / `unknown`); specific origins like `qr` live as plain strings. The middleware persists it as-is, and Filament shows it as a plain badge.

### Step 2 — Build the QR URL

```
https://simonecerruti.com/?utm_source=qr&utm_medium=print&utm_campaign=card-qr-2026
```

Encode that exact URL into the QR. Use a stable generator (a static PNG from `qr-code-generator.com` or `qrencode` CLI works — anything that just encodes the URL, no redirect service). Embedding the URL directly into the QR means:

- the URL works forever, even if the analytics system is rewritten
- there's no third-party that can disappear, change pricing, or log your scans

```bash
# Example generation via qrencode (PNG, 8 modules per pixel, error correction level Q)
qrencode -s 8 -l Q -o card-qr-2026.png \
  'https://simonecerruti.com/?utm_source=qr&utm_medium=print&utm_campaign=card-qr-2026'
```

Test the printed card by scanning it with your phone before the full print run. The landing should hit `/it/` (or `/en/` depending on the phone locale), and the session in the admin should show:

- **Campaign** = `Business card QR — 2026 batch`
- **Source** = `qr`
- **Medium** = `print`
- **Referrer** = empty (the QR doesn't pass a referrer header)
- **Device type** = mobile (almost always; QR scans happen on phones)

### Step 3 — Why the QR campaign is interesting in a different way

A QR campaign is a long-tail signal, not a spike. Cards get handed out over weeks. Look at:

- **VisitsByDayChart** — slow, steady stream rather than a launch spike. Notice spikes that correlate with specific events you attended.
- **TopCampaignsTable** — the QR campaign's `Visits` count gives a rough "scan rate" across the print run lifetime.
- **TopPagesTable** — what pages people went to *after* the home page. The QR lands them on `/`, so any page that ranks high here is one the visitor actively navigated to.
- Filter `/admin/visit-sessions` by Campaign = QR + Device type = mobile + Country = wherever you handed out cards. Confirms the audience is who you think it is.

### Step 4 — Reprint scenarios

Two cases:

- **Same design, same audience.** Reuse the existing campaign and the same slug. The print run is just more cards. Update the campaign `notes` with the new print date.
- **New design or new audience (e.g. conference-specific cards).** Create a new campaign with a different slug (`card-qr-2026-conf-x`). This way you can compare scan rates between print runs.

### Step 5 — When to retire

If you decide to retire the QR (new card design with a new slug), set `ends_at = now()` on the old campaign (or use the **End now** action on the campaigns list). Existing sessions stay bound; new scans on still-circulating old cards fall through to the raw-UTM path.

---

## What the two examples share

Both reduce to:

1. Create a campaign with a stable slug.
2. Build a URL: `?utm_source=...&utm_medium=...&utm_campaign=<slug>`.
3. Use that URL anywhere — paid ad, QR, newsletter, email signature, Slack post.
4. Verify in `/admin/visit-sessions` with a private-window test.
5. Read the data via widgets + the visit-sessions filtered view.

Anywhere you can put a URL, you can attach a campaign. The slug is the binding contract.
