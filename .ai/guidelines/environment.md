# Environment


## Dev Environment
- IDE: VSCode
- Host Machine: Docker container running custom image, see docker/Dockerfile.
- Same image of production, plus some customization made in a dedicated if block in the docker/Dockerfile.
- Runtime: Supervisor-managed processes: nginx, php-fpm, cron, queue workers, and (dev only) vite watch build and the Playwright orphan reaper.

### Logs

All logs live in `storage/logs`:

- `nginx.log`
- `nginx_access.log`
- `nginx_error.log`
- `php-fpm.log`
- `laravel.log`
- `cron.log`
- `vite-watch.log`
- `supervisord.log`
- `worker.log`

#### Reading logs

- Read Laravel logs via Boost MCP: `read-log-entries` for recent entries, `last-error` for the most recent exception — prefer these over `cat`/`grep`-ing `laravel.log`.
- Tail live logs with `php artisan pail`.
- Browser / JS runtime errors: the `browser-logs` Boost MCP tool.

## Production Environment

- Same Dockerfile as dev (nginx, php-fpm, cron, queue workers): see docker/Dockerfile. Opcache enabled (no CLI).
- Traefik runs in front of the app and routes by the labels in `deploy/compose.yml`.
- CI builds the image once, tagged with the commit SHA, and pushes it to Docker Hub. Deploys use the image digest, never a tag. See `.github/workflows`.
- CI deploys by running `deploy/gateway.sh` with a fixed verb over a restricted SSH key. The gateway validates `deploy/compose.yml` against a policy before applying it.
- Migrations run in a throwaway container first. Only then does a zero-downtime `docker rollout` replace the app container; `rebuild-caches.sh` rebuilds the Laravel caches afterwards.
- Flow: merge into `staging` deploys staging; the Release workflow deploys production with the image already tested in staging; the Hotfix workflow is the only case where production builds its own image.
- Rollback redeploys a previous tag; migrations are not rolled back (expand / contract).
- All logs in storage/logs.
- Details, server setup and operations: `deploy/README.md`.
