# Environment


## Dev Environment
- IDE: VSCode
- Host Machine: Docker container running custom image, see docker/Dockerfile.
- Same image of production, plus some customization made in a dedicated if block in the docker/Dockerfile.
- Runtime:  Supervisor managed processes for: nginx, php-fpm, cron, queues, vite watch build (dev only)

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
- Same Dockerfile (nginx, php-fpm, cron, queues): see docker/Dockerfile
- Served by traefik
- Custom docker-compose.yml, similar to docker-compose.local.yml, without mailpit but with watchtower
- CI/CD by Github Actions, see .github/workflows/deploy.yml
- After watchtower updates the container, by pulling the image from dockerhub, it triggers the /post-update.sh script (locally located in docker/scripts//post-update.sh)
- Opcache enabled (no CLI)
- All logs in storage/logs
