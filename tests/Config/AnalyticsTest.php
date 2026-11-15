<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

describe('pan allow-list', function (): void {
    it('whitelists every static data-pan used in the views', function (): void {
        $allowed = config()->array('analytics.pan.allowed_analytics');
        $offenders = [];

        foreach (File::allFiles(resource_path('views')) as $file) {
            if (! \str_ends_with($file->getFilename(), '.blade.php')) {
                continue;
            }

            \preg_match_all('/data-pan="([^"]+)"/', $file->getContents(), $matches);

            foreach ($matches[1] as $pan) {
                if (\str_contains($pan, '{{') || \str_contains($pan, '$') || \str_contains($pan, '@')) {
                    continue;
                }

                if (! \in_array($pan, $allowed, true)) {
                    $offenders[] = "{$file->getRelativePathname()} uses the data-pan \"{$pan}\" missing from analytics.pan.allowed_analytics";
                }
            }
        }

        expect($offenders)->toBeEmpty();
    });

    it('keeps the allow-list within the tracked-names cap', function (): void {
        expect(\count(config()->array('analytics.pan.allowed_analytics')))
            ->toBeLessThanOrEqual(config()->integer('analytics.pan.max_analytics'));
    });
});
