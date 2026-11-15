<?php

declare(strict_types=1);

use App\Enums\DeviceType;
use App\Support\DeviceTypeDetector;

describe('detect', function (): void {
    it('detects the expected device type', function (string $user_agent, DeviceType $expected): void {
        expect(DeviceTypeDetector::detect($user_agent))->toBe($expected);
    })->with('user_agents');
});
