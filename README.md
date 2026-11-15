Simone Cerruti Website

## Development

The project runs in the dev container defined by `docker-compose.local.yml` (app, Postgres, Mailpit). Open the folder in VS Code and reopen it in the container, then:

```
composer setup
composer dev
```

Quality scripts: `composer test`, `composer pint`, `composer rector`, `composer stan`, `composer qa`. Each has an `:agentic` variant with machine-readable output.

AI tooling (Boost guidelines, skills, hooks) is regenerated with `composer ai:update`; see [`docs/ai-setup.md`](docs/ai-setup.md).

## Deploy

Images are built by GitHub Actions and rolled out to the server through a restricted SSH gateway: merges into `staging` deploy to staging, the **Release** workflow promotes the same image to production. Server setup, GitHub secrets and the cutover checklist are in [`docs/deploy.md`](docs/deploy.md) and [`deploy/README.md`](deploy/README.md).

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
