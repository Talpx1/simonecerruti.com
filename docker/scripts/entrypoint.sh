#!/bin/bash
set -e

# Load .env from the mounted project so templates below see current values.
# Laravel reads .env on its own at runtime; this is only for entrypoint/template substitution.
if [ -f /var/www/html/.env ]; then
    set -a
    # shellcheck disable=SC1091
    . /var/www/html/.env
    set +a
fi

export APP_WORKERS_AMOUNT=${APP_WORKERS_AMOUNT:-2}
export APP_WORKERS_MEMORY_LIMIT=${APP_WORKERS_MEMORY_LIMIT:-256M}
export FASTCGI_HTTPS=${FASTCGI_HTTPS:-$([ "$APP_ENV" = "local" ] && echo off || echo on)}

envsubst < /usr/local/etc/php/conf.d/opcache.ini.template \
         > /usr/local/etc/php/conf.d/opcache.ini

envsubst < /etc/supervisor/conf.d/laravel-worker.conf.template \
         > /etc/supervisor/conf.d/laravel-worker.conf

envsubst '${FASTCGI_HTTPS}' < /etc/nginx/sites-available/default.template \
                            > /etc/nginx/sites-available/default

# ---- Refresh Cloudflare IPs at startup (best effort) ----
/usr/local/bin/cloudflare-ips.sh || echo "Could not refresh Cloudflare IPs at startup"

if [ -f /var/www/html/artisan ] && [ ! -L /var/www/html/public/storage ]; then
    php /var/www/html/artisan storage:link || echo "[WARN] storage:link failed (non-blocking)"
fi

# Generate the sitemap so a freshly-started container serves /sitemap.xml right away:
# public/sitemap.xml is not baked into the image, and the daily scheduler only refreshes it
# at 01:00. Run as www-data so the scheduled regeneration (also www-data) can overwrite it.
# Non-blocking: a transient failure (e.g. DB not yet reachable) must not stop the container.
if [ -f /var/www/html/artisan ]; then
    su www-data -s /bin/bash -c "php /var/www/html/artisan app:generate-sitemap" \
        || echo "[WARN] sitemap generation failed (non-blocking)"
fi

# Build the CodeGraph index on first boot only — it lives in the bind-mounted repo, so it
# survives rebuilds and must not be re-indexed every start. Runs as www-data: the entrypoint is
# root, and a root-owned .codegraph/ would lock out the daemon that later writes to it.
if [ "$APP_ENV" = "local" ] && [ ! -d /var/www/html/.codegraph ] \
    && command -v codegraph >/dev/null 2>&1; then
    su www-data -s /bin/bash -c 'HOME=/var/www codegraph init /var/www/html' \
        || echo "[WARN] codegraph init failed (non-blocking)"
fi

if [ ! -f /var/www/html/preload.php ]; then
    echo "[WARN] preload.php missing - opcache preload disabled"
fi

exec "$@"
