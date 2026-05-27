#!/bin/bash

# Re-deployment hook invoked by Watchtower after image update.
# Rebuilds Laravel/Filament caches first, then gracefully bounces services.

set -euo pipefail
IFS=$'\n\t'

LOG_FILE="/var/www/html/storage/logs/post-update.log"
mkdir -p "$(dirname "$LOG_FILE")"

log() {
    printf '[%s] %s\n' "$(date -u +'%F %T')" "$*" | tee -a "$LOG_FILE"
}

run() {
    local mode="$1" description="$2"
    shift 2

    log "${description}..."
    if "$@" >>"$LOG_FILE" 2>&1; then
        log "${description}: OK"
        return 0
    fi

    if [ "$mode" = "fatal" ]; then
        log "[FATAL] ${description} failed - aborting"
        exit 1
    fi

    log "[WARN] ${description} failed (non-blocking)"
}

log "==> Start post-update"
cd /var/www/html || { log "[FATAL] cd /var/www/html failed"; exit 1; }

# Rebuild caches BEFORE bouncing services so the new processes boot with valid caches.
run fatal "Running migrations"                  php artisan migrate --force
run fatal "Optimizing app"                      php artisan optimize
run fatal "Clearing route cache"                php artisan route:clear
run fatal "Caching translated routes"           php artisan route:trans:cache
run fatal "Optimizing filament"                 php artisan filament:optimize
run fatal "Fixing cache ownership"              chown -R www-data:www-data storage bootstrap/cache

# Graceful: signal workers/scheduler to finish current job and exit; supervisor respawns them.
run warn  "Signalling queue workers to restart" php artisan queue:restart
run warn  "Interrupting scheduler"              php artisan schedule:interrupt

# Apply any supervisor config changes shipped with the new image.
run warn  "Re-reading supervisor config"        supervisorctl reread
run warn  "Updating supervisor config"          supervisorctl update
run warn  "Reloading crontab"                   crontab /etc/crontab

# SIGHUP = nginx graceful reload (workers finish in-flight requests). Supervisor has no native reload.
run fatal "Reloading nginx (SIGHUP)"            supervisorctl signal HUP nginx

# Hard restart php-fpm: wipes OPcache and re-runs preload.php with the new bytecode.
run fatal "Restarting php-fpm"                  supervisorctl restart php-fpm

log "==> Post-update completed successfully"

printf '\n\n\n\n\n' >>"$LOG_FILE"
