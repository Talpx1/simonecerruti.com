Simone Cerruti Website

## Internal analytics — UTM campaign URLs

Internal traffic statistics live in `visit_sessions` / `page_views` and surface in the Filament admin (`/admin`) under the **Visit sessions** resource and the dashboard widgets.

To wire an outbound link to a tracked campaign:

1. Create a campaign from `/admin/campaigns` and pick a stable `slug` (kebab-case).
2. Append the standard UTM parameters to the URL you publish. The middleware matches `utm_campaign` against `campaigns.slug` when the campaign is currently active (`starts_at <= now() <= ends_at`, both nullable).

```
https://simonecerruti.com/?utm_source=instagram&utm_medium=social&utm_campaign=<campaign-slug>
```

- `utm_source` — origin (e.g. `instagram`, `newsletter`, `qr`). Falls back as `source` when no matching campaign exists.
- `utm_medium` — channel from the `VisitMediumType` enum (`social`, `email`, `organic`, `paid`, `display`, `referral`, `affiliate`, `print`, `physical`). Optional.
- `utm_campaign` — must equal the `slug` of a currently-active campaign for the visit to bind to it.
- `utm_term`, `utm_content` — captured raw alongside the others; `utm_content` is the conventional slot for in-campaign variants.

Unknown / out-of-window `utm_campaign` values are still recorded on the visit; they just don't bind to any campaign in the admin.

Full reference: [`docs/analytics/README.md`](docs/analytics/README.md).
