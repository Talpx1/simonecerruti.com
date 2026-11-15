<?php

declare(strict_types=1);

describe('disks', function (): void {
    it('defines the Google Drive disk used for the offsite backup', function (): void {
        expect(config()->string('filesystems.disks.google.driver'))->toBe('google');
    });
});
