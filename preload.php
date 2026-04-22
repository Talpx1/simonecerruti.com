<?php

declare(strict_types=1);

if (! ini_get('opcache.enable')) {
    return;
}

error_reporting(E_ERROR);

$count = 0;

function preload(string $file, int &$count): void {
    if (is_file($file)) {
        @opcache_compile_file($file);
        $count++;
    }
}

function preloadDir(string $path, int &$count, bool $recursive = false): void {
    if (! is_dir($path)) {
        return;
    }

    if ($recursive) {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)
        );
    } else {
        $iterator = new DirectoryIterator($path);
    }

    foreach ($iterator as $file) {
        if (! ($file instanceof SplFileInfo) || $file->getExtension() !== 'php') {
            continue;
        }

        $pathname = $file->getPathname();

        if (
            str_contains($pathname, '/Tests/') ||
            str_contains($pathname, '/tests/') ||
            str_ends_with($pathname, 'Test.php')
        ) {
            continue;
        }

        preload($pathname, $count);
    }
}

$base = '/var/www/html';

$vendor_dirs = [
    '/vendor/laravel/framework/src/Illuminate/Support',
    '/vendor/laravel/framework/src/Illuminate/Http',
    '/vendor/laravel/framework/src/Illuminate/Routing',
    '/vendor/laravel/framework/src/Illuminate/Container',
    '/vendor/laravel/framework/src/Illuminate/Database',
    '/vendor/laravel/framework/src/Illuminate/View',
    '/vendor/laravel/framework/src/Illuminate/Pipeline',
    '/vendor/laravel/framework/src/Illuminate/Events',
    '/vendor/laravel/framework/src/Illuminate/Auth',
    '/vendor/laravel/framework/src/Illuminate/Cache',
    '/vendor/laravel/framework/src/Illuminate/Log',
];

foreach ($vendor_dirs as $dir) {
    preloadDir($base.$dir, $count, recursive: true);
}

$app_dirs = [
    '/app/Models',
    '/app/Http/Controllers',
    '/app/Http/Middleware',
    '/app/Providers',
    '/app/Services',
    '/app/Livewire',
    '/app/Filament',
];

foreach ($app_dirs as $dir) {
    preloadDir($base.$dir, $count, recursive: true);
}

error_log("OPcache preload: {$count} files compiled.");
