# Deploy

Deploys come from the `laravel-containerized-starter-kit` deploy module. The reference for the gateway and the compose file is [`deploy/README.md`](../deploy/README.md); this page covers what is specific to this project.

## Flow

1. Merging into `staging` builds an image tagged with the commit SHA and deploys it to staging.
2. The **Release** workflow tags `vX.Y.Z`, fast-forwards the `production` branch and promotes the same image, deployed by digest. A hotfix builds its own image.
3. CI reaches the server only by running `deploy/gateway.sh` over SSH with a per-environment key.
4. Migrations run in a throwaway container, then `docker rollout` replaces the app container without downtime.
5. Each container start runs `docker/scripts/rebuild-caches.sh` (config, route, localized route and Filament caches).

`/up` fails when the database is unreachable (`App\Listeners\CheckDatabaseConnection`), so it is the health signal for the healthcheck and the rollout.

## Docker image

- The Docker Hub repository is `talpx1/simone_cerruti_website` and must exist and be private.
- The gateway application name is `simone-cerruti-website` (the gateway accepts only `[a-z0-9-]`).
- Opcache preload uses `preload.php` through `PHP_OPCACHE_PRELOAD=/var/www/html/preload.php`. Filament must stay out of the preload list.

## Server prerequisites

- `jq`, `flock`, Docker Compose and the `docker-rollout` plugin.
- A `deploy` user in the `docker` group, with `deploy/gateway.sh` installed as `/usr/local/bin/deploy-gateway`.
- Per environment: `/var/www/simone-cerruti-website/<env>/state` owned by `deploy`, and a root-owned `deploy.conf` with `ALLOWED_HOSTS`.
- One restricted SSH key per environment in `authorized_keys`: `restrict,command="/usr/local/bin/deploy-gateway simone-cerruti-website <env>"`.
- Traefik with an external network named `traefik`, an entrypoint `websecure` and a certresolver `cloudflare` (override with `TRAEFIK_ENTRYPOINT` and `TRAEFIK_CERTRESOLVER`).
- A staging environment: production promotes the staging image.

## GitHub configuration

- Variables: `DEPLOY_HOST`, `DOCKERHUB_USERNAME`, `DOCKERHUB_NAMESPACE`, `RELEASE_APP_ID`.
- Secrets: `DEPLOY_SSH_KEY_STAGING`, `DEPLOY_SSH_KEY_PRODUCTION`, `DEPLOY_KNOWN_HOSTS`, `DOTENV_STAGING`, `DOTENV_PRODUCTION`, `DOCKERHUB_TOKEN`, `DOCKERHUB_PULL_TOKEN`, `RELEASE_APP_PRIVATE_KEY`. Optional: `SECURITY_ALERT_WEBHOOK_URL`, `GITLEAKS_LICENSE`.
- Environments `staging` and `production`, a `staging` branch, and branch protection on `main`, `staging` and `production` with the `pr-checks` checks required.
- `DOTENV_PRODUCTION` must set `APP_URL`, `APP_HOST`, `DB_*`, every `PHP_OPCACHE_*` value (no defaults) and the application keys (reCAPTCHA, backup, Slack, mail). Drop `COMPOSE_PROJECT_NAME`.

## Moving from watchtower

1. Put the old site in maintenance mode.
2. Take `pg_dump -Fc` of the database and an archive of `storage/` (it holds the media).
3. Create the `simone-cerruti-website-production_{pgsql_data,storage}` volumes and restore both, the database into Postgres 18.
4. Run **Deploy production**; the migration step is then a no-op.
5. Stop the old stack and remove watchtower and the old `post-update.sh` flow.
6. Monitor `/up`.

## Local Postgres 18

The local database moved from Postgres 17 to 18, with the volume mounted at `/var/lib/postgresql`. Dump with `pg_dumpall`, remove the old volume, start the new `db` service, restore with `psql -f`, then recreate the per-worktree test databases.
