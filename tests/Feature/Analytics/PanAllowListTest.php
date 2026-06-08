<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

it('whitelists every static data-pan used in the views', function () {
    $allowed = config('analytics.pan.allowed_analytics');

    $offenders = [];

    foreach (File::allFiles(resource_path('views')) as $file) {
        if (! str_ends_with($file->getFilename(), '.blade.php')) {
            continue;
        }

        preg_match_all('/data-pan="([^"]+)"/', (string) $file->getContents(), $matches);

        foreach ($matches[1] as $pan) {
            // Dynamically rendered names (slug/prop interpolation) cannot be
            // resolved statically; they are validated where they are emitted.
            if (str_contains($pan, '{{') || str_contains($pan, '$') || str_contains($pan, '@')) {
                continue;
            }

            if (! in_array($pan, $allowed, true)) {
                $offenders[] = $file->getRelativePathname().' → '.$pan;
            }
        }
    }

    expect($offenders)->toBe([]);
});
