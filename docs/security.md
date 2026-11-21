# Security baseline

## Mass assignment

Models are never unguarded. Every model declares the attributes it accepts with `#[Fillable([...])]`. `Model::unguard()` and `$guarded = []` are forbidden (an architecture test enforces it). When a column must be set by code only, leave it out of the list and set it explicitly.

## Strict models

`Model::shouldBeStrict()` is active outside production: lazy loading, silently discarded attributes and missing attributes throw. In production the same mistakes do not take the site down.

## Passwords

`Password::defaults()` requires at least 12 characters with letters, mixed case, numbers and symbols. Use `Password::default()` in every password validation rule, including the Filament panel.

## Sessions

Sessions are serialized as JSON (`session.serialization`), so a leaked `APP_KEY` cannot be turned into a PHP object-injection attack. Session values must be JSON-serializable.

## HTTP security headers

`bepsvpt/secure-headers` runs on the `web` middleware group and is configured in `config/secure-headers.php`. HSTS is sent only when `APP_ENV` is `production` (or `staging`, if one is ever added). The Content Security Policy runs in `report-only` mode: it reports violations without blocking anything, so Livewire, Filament, reCAPTCHA and the inline scripts keep working. Review the reports, then switch `report-only` off once the policy covers every page. `X-Frame-Options` stays `sameorigin`.

Behind Cloudflare the app trusts proxies only from the Docker address ranges (`172.16.0.0/12`, `10.0.0.0/8`), and `REAL_IP_HEADER` defaults to `CF-Connecting-IP` but can be overridden from the environment.

## Slow query log

`overtrue/laravel-query-logger` writes queries slower than `DB_SLOW_QUERY_THRESHOLD_MS` (default 500) to `storage/logs/slow-queries.log`. Disable it with `DB_SLOW_QUERY_LOG_ENABLED=false`.

## Activity log

The activity log (`spatie/laravel-activitylog` v5) stores tracked changes in `attribute_changes`; `properties` holds only custom data. Read changes with `$activity->attribute_changes`. `activitylog:clean` runs daily at 02:00 and keeps `ACTIVITYLOG_RETENTION_DAYS` days (default 30).

## Queue

`retry_after` of the queue connections (660 seconds) must stay above the worker `--timeout` in `docker/configs/laravel-worker-supervisor.conf` (600), or a running job is handed to a second worker. A test enforces it.
