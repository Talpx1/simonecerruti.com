# Deploy

Deploys come from the `laravel-containerized-starter-kit` deploy module. The reference for the gateway and the compose file is [`deploy/README.md`](../deploy/README.md); this page covers what is specific to this project.

## Flow

1. Features are merged into `main` (the default branch) and tested locally and in CI. There is no staging environment: the only way to deploy is the manual **Release** workflow.
2. **Release** re-runs the test suite on the `main` commit, creates the release commit and the tag `vX.Y.Z`, fast-forwards the `production` branch and publishes the GitHub Release. The **Deploy production** workflow then builds the image for the tag commit and deploys it by digest.
   Rollback is a manual run of Deploy production with an existing tag. There is no hotfix workflow: urgent fixes go through `main` and a patch Release. Nothing is merged directly into `production`.
3. CI reaches the server only by running the deploy gateway over SSH with the `production` key. The gateway lives in its own repository (`laravel-deploy-gateway`); this project pins the release it requires in `deploy/gateway.version` and its hash in `deploy/gateway.sha256`, and the deploy fails if the server runs another one.
4. Migrations run in a throwaway container, then `docker rollout` replaces the app container without downtime.
5. Each container start runs `docker/scripts/rebuild-caches.sh` (config, route, localized route, Filament and data structure caches).
6. The image build runs `.github/scripts/check-image.sh`: the code is owned by root and not writable by the app user, build-only files are absent, and a first boot on an empty database works.

`/up` fails when the database is unreachable (`App\Listeners\CheckDatabaseConnection`), so it is the health signal for the healthcheck and the rollout.

## Docker image

- The Docker Hub repository is `talpx1/simone_cerruti_website` and must exist and be private.
- The gateway application name is `simone-cerruti-website` (the gateway accepts lowercase letters, digits, `.` and `-`).
- `public/sitemap.xml` is a symlink to `storage/app/sitemap.xml`, where `app:generate-sitemap` writes: the code stays read-only and the file survives in the `storage` volume. The entrypoint creates the link if it is missing.
- Opcache preload uses `preload.php` through `PHP_OPCACHE_PRELOAD=/var/www/html/preload.php`. Filament must stay out of the preload list.

## Server prerequisites

- Docker with Compose, `jq`, `flock`, `logger`, `curl` and `timeout`.
- The gateway release in `deploy/gateway.version`, installed once with `install.sh --expect-sha256 <contents of deploy/gateway.sha256>`. It creates the `deploy` user, `docker-rollout`, `yq` and the admin tools.
- Once, for the only environment, `deploy-gateway-add-app simone-cerruti-website production "<host>" <key.pub> ALLOWED_IMAGE_REPOS="docker.io/<DOCKERHUB_NAMESPACE>/simone_cerruti_website postgres"`. It creates the root-owned `deploy.conf`, the `state` directory, the proxy network `traefik-<id>-<env>` and the restricted `authorized_keys` line.
- Traefik with an entrypoint `websecure` and a certresolver `cloudflare` (override with `TRAEFIK_ENTRYPOINT` and `TRAEFIK_CERTRESOLVER`), the container named `traefik`. After recreating it run `sudo deploy-gateway-connect-traefik`.

The server was set up with the previous gateway: install the new release, then run `deploy-gateway-add-app` again for `production` and deploy.

## GitHub configuration

- Everything is a repository secret, there are no variables: `DEPLOY_HOST`, `DEPLOY_SSH_KEY_PRODUCTION`, `DEPLOY_KNOWN_HOSTS`, `DOTENV_PRODUCTION`, `DOCKERHUB_USERNAME`, `DOCKERHUB_NAMESPACE`, `DOCKERHUB_TOKEN`, `DOCKERHUB_PULL_TOKEN`, `RELEASE_APP_ID`, `RELEASE_APP_PRIVATE_KEY`. Optional: `GATEWAY_REPOSITORY` and `GATEWAY_REPO_TOKEN` (only if the gateway repository is not the default one or is private), `SECURITY_ALERT_WEBHOOK_URL`, `GITLEAKS_LICENSE`.
- One environment, `production`. Branches: `main` (default) and `production`, both protected with the `pr-checks` checks (including `compose-policy`) required. Only the release GitHub App may push to `production`. Auto-merge is not needed.
- `DOTENV_PRODUCTION` must set `APP_URL`, `APP_HOST`, `DB_*`, every `PHP_OPCACHE_*` value (no defaults) and the application keys (reCAPTCHA, `BACKUP_*`, `GOOGLE_DRIVE_*`, `ANTHROPIC_API_KEY` for the [AI content generation](ai-content-generation.md), Slack, mail). Drop `COMPOSE_PROJECT_NAME`.

## Backups

`backup:clean` (01:00), `backup:run` (01:30) and `backup:monitor` (03:00) run every night and save the database and files to the disks listed in `BACKUP_DISKS`: `local` (same volume as the app, so not an offsite copy) and `google` (Google Drive). Set `BACKUP_ENABLED=false` in the `.env` to skip the three scheduled commands.

1. In Google Cloud Console create a project, enable the **Google Drive API** and create an OAuth client (type *Web application*, redirect `https://developers.google.com/oauthplayground`).
2. On [OAuth Playground](https://developers.google.com/oauthplayground) use your own credentials, authorize the scope `https://www.googleapis.com/auth/drive` and exchange the code for a **refresh token**.
3. Set `GOOGLE_DRIVE_CLIENT_ID`, `GOOGLE_DRIVE_CLIENT_SECRET`, `GOOGLE_DRIVE_REFRESH_TOKEN` and `GOOGLE_DRIVE_FOLDER` (`Backups/simone_cerruti_website`) in `DOTENV_PRODUCTION`.
4. Keep the OAuth consent screen in *Production*: in *Testing* the refresh token expires after 7 days.
5. Set `BACKUP_ARCHIVE_PASSWORD` to encrypt the archives and `BACKUP_NOTIFICATION_EMAIL` to receive failures.
6. Run `php artisan backup:run` once and check the file in Drive, then check once that a restore works.

## Admin multi-factor authentication

The admin panel requires multi-factor authentication: an authenticator app (with recovery codes) or an email code. Users without a method are sent to the setup page at their next sign-in. Email codes need working mail; the cache store must support locks (`database`, `redis`, `file`, `memcached`).

A user who lost the second factor is reset from the server with `php artisan users:reset-mfa {email}`, then sets it up again at the next sign-in.

## Moving from watchtower

1. Put the old site in maintenance mode.
2. Take `pg_dump -Fc` of the database and an archive of `storage/` (it holds the media).
3. Create the `simone-cerruti-website-production_{pgsql_data,storage}` volumes and restore both, the database into Postgres 18.
4. Run **Deploy production**; the migration step is then a no-op.
5. Stop the old stack and remove watchtower and the old `post-update.sh` flow.
6. Monitor `/up`.

## Local Postgres 18

The local database moved from Postgres 17 to 18, with the volume mounted at `/var/lib/postgresql`. Dump with `pg_dumpall`, remove the old volume, start the new `db` service, restore with `psql -f`, then recreate the per-worktree test databases.
