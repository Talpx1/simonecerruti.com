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

`bepsvpt/secure-headers` runs on the `web` middleware group and is configured in `config/secure-headers.php`. The Content Security Policy is disabled on purpose until a policy that covers Livewire, Filament, reCAPTCHA and the inline scripts is written and tested in a browser. Enable it with `csp.enable` and start with `report-only`.

## Slow query log

`overtrue/laravel-query-logger` writes queries slower than `DB_SLOW_QUERY_THRESHOLD_MS` (default 500) to `storage/logs/slow-queries.log`. Disable it with `DB_SLOW_QUERY_LOG_ENABLED=false`.

## Activity log

The activity log (`spatie/laravel-activitylog` v5) stores tracked changes in `attribute_changes`; `properties` holds only custom data. Read changes with `$activity->attribute_changes`.
