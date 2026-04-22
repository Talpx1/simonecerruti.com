#!/bin/bash
#see https://gist.github.com/mohanpedala/1e2ff5661761d3abd0385e8223e16425?permalink_comment_id=3935570
set -euo pipefail
IFS=$'\n\t'
{
    echo "Start post-update script"
    cd /var/www/html || {
        echo "[FATAL] Failed to cd into /var/www/html"
        exit 1
    }

    echo "Linking storage"
    if [ ! -L "/var/www/html/public/storage" ]; then
        if php artisan storage:link; then
            echo "Storage linked"
        else
            echo "[WARN] php artisan storage:link failed (non-blocking)"
        fi
    else
        echo "Storage was already linked"
    fi

    echo "Syncing Enums"
    if php artisan enums:sync; then
        echo "Enums synced"
    else
        echo "[FATAL] php artisan enums:sync failed"
        exit 1
    fi

    echo "Running migrations"
    if php artisan migrate --force; then
        echo "Migrations run"
    else
        echo "[FATAL] php artisan migrate --force failed"
        exit 1
    fi

    echo "Optimizing app"
    if php artisan optimize; then
        echo "App optimized"
    else
        echo "[FATAL] php artisan optimize failed"
        exit 1
    fi

    echo "Caching translated routes"
    if php artisan route:clear && php artisan route:trans:cache; then
        echo "Translated routes cached"
    else
        echo "[FATAL] php artisan route:clear && php artisan route:trans:cache failed"
        exit 1
    fi

    echo "Optimizing filament"
    if php artisan filament:optimize; then
        echo "Filament optimized"
    else
        echo "[FATAL] php artisan filament:optimize failed"
        exit 1
    fi

    echo "Setting cache permissions"
    if chown -R www-data:www-data storage bootstrap/cache; then
        echo "Cache permissions set"
    else
        echo "[FATAL] chown -R www-data:www-data storage bootstrap/cache failed"
        exit 1
    fi

    echo "Interrupting schedule"
    if php artisan schedule:interrupt; then
        echo "Schedule interrupted"
    else
        echo "[WARN] php artisan schedule:interrupt failed (non-blocking)"
    fi

    echo "Reading supervisor config"
    if supervisorctl reread; then
        echo "Supervisor config read"
    else
        echo "[WARN] supervisorctl reread failed (non-blocking)"
    fi

    echo "Updating supervisor config"
    if supervisorctl update; then
        echo "Supervisor config updated"
    else
        echo "[WARN] supervisorctl update failed (non-blocking)"
    fi

    echo "Restarting nginx"
    if supervisorctl restart nginx; then
        echo "Restarted nginx"
    else
        echo "[FATAL] supervisorctl restart nginx failed"
        exit 1
    fi

    echo "Restarting php-fpm"
    if supervisorctl restart php-fpm; then
        echo "Restarted php-fpm"
    else
        echo "[FATAL] supervisorctl restart php-fpm failed"
        exit 1
    fi

    echo "Restarting workers"
    if supervisorctl restart laravel-worker:*; then
        echo "Restarted workers"
    else
        echo "[FATAL] supervisorctl restart laravel-worker:* failed"
        exit 1
    fi

    echo "Restarting cron"
    if supervisorctl restart laravel-cron; then
        echo "Restarted cron"
    else
        echo "[FATAL] supervisorctl restart laravel-cron failed"
        exit 1
    fi

    echo "Reading crontab"
    if crontab /etc/crontab; then
        echo "Crontab read"
    else
        echo "[WARN] crontab /etc/crontab failed (non-blocking)"
    fi

    echo "Restarting Laravel queues"
    if php artisan queue:restart; then
        echo "Queues restarted"
    else
        echo "[WARN] php artisan queue:restart failed (non-blocking)"
    fi

    echo "Post-update completed successfully"
} >> "/var/www/html/storage/logs/post-update-$(date +"%F-%H_%M_%S").log" 2>&1
exit 0
