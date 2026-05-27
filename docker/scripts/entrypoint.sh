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

if [ -f /var/www/html/artisan ] && [ ! -L /var/www/html/public/storage ]; then
    php /var/www/html/artisan storage:link || echo "[WARN] storage:link failed (non-blocking)"
fi

if [ ! -f /var/www/html/preload.php ]; then
    echo "[WARN] preload.php missing - opcache preload disabled"
fi

exec "$@"