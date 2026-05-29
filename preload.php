<?php

declare(strict_types=1);

if (! ini_get('opcache.enable')) {
    return;
}

require_once __DIR__.'/vendor/autoload.php';

/** @var array<string, string> $classMap */
$classMap = require __DIR__.'/vendor/composer/autoload_classmap.php';

// Whitelisted prefixes (relative to the project root). Only the framework,
// Livewire and the hot first/third-party packages that sit on every request
// are preloaded. Each path is verified to exist in `vendor/`.
//
// Filament is intentionally excluded: PHP 8.5 preload corrupts BackedEnum
// cases used as static property defaults (e.g. BasePage::$formActionsAlignment
// = Alignment::Start), causing TypeError on Filament\Auth\Pages\Login render.
$includePrefixes = [
    'vendor/laravel/framework/src/Illuminate/',
    'vendor/livewire/livewire/src/',
    'vendor/nesbot/carbon/src/',
    'vendor/mcamara/laravel-localization/src/',
    'vendor/lara-zeus/spatie-translatable/src/',
    'vendor/lorisleiva/laravel-actions/src/',
    'vendor/spatie/laravel-activitylog/src/',
    'vendor/spatie/laravel-medialibrary/src/',
    'vendor/spatie/laravel-tags/src/',
    'vendor/spatie/laravel-translatable/src/',
    'vendor/spatie/laravel-sitemap/src/',
    'vendor/thecodingmachine/safe/',
    'app/',
];

// Substring matches. App-only exclusions are deliberately scoped with the `app/`
// prefix so framework classes under e.g. `Illuminate/Console/...` stay preloaded.
// Service providers, console commands and database files have side effects or are
// not part of the request hot-path, so they are skipped.
$excludeFragments = [
    '/tests/',
    '/Tests/',
    'Test.php',
    'Illuminate/Testing/',
    'Illuminate/Foundation/Testing/',
    'app/Console/',
    'app/Providers/',
    'database/migrations/',
    'database/factories/',
    'database/seeders/',
];

$base = __DIR__.'/';
$loaded = 0;
$skipped = 0;
$errors = 0;
$start = microtime(true);

foreach ($classMap as $class => $file) {
    if (! is_string($class) || ! is_string($file)) {
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

    if ($matches) {
        foreach ($excludeFragments as $fragment) {
            if (str_contains($relative, $fragment)) {
                $matches = false;
                break;
            }
        }
    }

    if (! $matches) {
        $skipped++;

        continue;
    }

    try {
        // Loading through the autoloader (rather than opcache_compile_file) links
        // each class against its parents, interfaces and traits, so it is added to
        // the preload set fully resolved instead of being skipped as "unlinked".
        if (class_exists($class) || interface_exists($class) || trait_exists($class) || enum_exists($class)) {
            $loaded++;
        } else {
            $skipped++;
        }
    } catch (Throwable) {
        // A class that cannot be linked standalone (e.g. it references an optional
        // dependency) is skipped rather than aborting the whole preload.
        $errors++;
    }
}

error_log(sprintf(
    '[preload] loaded=%d skipped=%d errors=%d duration=%ss',
    $loaded,
    $skipped,
    $errors,
    round(microtime(true) - $start, 3)
));
