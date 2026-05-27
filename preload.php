<?php

declare(strict_types=1);

if (! function_exists('opcache_compile_file') || ! ini_get('opcache.enable')) {
    return;
}

require_once __DIR__.'/vendor/autoload.php';

$classMap = require __DIR__.'/vendor/composer/autoload_classmap.php';

$includePrefixes = [
    'vendor/laravel/framework/src/Illuminate/',
    'vendor/livewire/livewire/src/',
    'vendor/filament/',
    'vendor/spatie/laravel-permission/src/',
    'vendor/spatie/laravel-activitylog/src/',
    'vendor/spatie/laravel-medialibrary/src/',
    'vendor/bezhansalleh/filament-shield/src/',
    'app/',
];

// Substring matches. App-only exclusions deliberately scoped with `app/` prefix
// so framework classes under `Illuminate/Console/...` stay precompiled.
$excludeFragments = [
    '/tests/',
    '/Tests/',
    'Test.php',
    'app/Console/',
    'app/Providers/',
    'database/migrations/',
    'database/factories/',
    'database/seeders/',
];

$base = __DIR__.'/';
$compiled = 0;
$skipped = 0;
$errors = 0;
$start = microtime(true);

foreach ($classMap as $file) {
    if (! is_string($file)) {
        continue;
    }

    $relative = str_starts_with($file, $base) ? substr($file, strlen($base)) : $file;

    $matches = false;
    foreach ($includePrefixes as $prefix) {
        if (str_starts_with($relative, $prefix)) {
            $matches = true;
            break;
        }
    }

    if (! $matches) {
        $skipped++;

        continue;
    }

    foreach ($excludeFragments as $fragment) {
        if (str_contains($relative, $fragment)) {
            $matches = false;
            break;
        }
    }

    if (! $matches) {
        $skipped++;

        continue;
    }

    try {
        if (@opcache_compile_file($file)) {
            $compiled++;
        } else {
            $errors++;
        }
    } catch (Throwable) {
        $errors++;
    }
}

error_log(sprintf(
    '[preload] compiled=%d skipped=%d errors=%d duration=%ss',
    $compiled,
    $skipped,
    $errors,
    round(microtime(true) - $start, 3)
));