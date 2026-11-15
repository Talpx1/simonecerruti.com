<?php

declare(strict_types=1);

use App\Support\BotSignalDetector;

describe('score', function (): void {
    it('scores request headers for automation signals', function (
        ?string $accept,
        ?string $accept_language,
        ?string $accept_encoding,
        ?string $sec_fetch_site,
        string $user_agent,
        int $expected,
    ): void {
        expect(BotSignalDetector::score($accept, $accept_language, $accept_encoding, $sec_fetch_site, $user_agent))
            ->toBe($expected);
    })->with('bot_header_scores');
});

describe('isAutomated', function (): void {
    it('flags a score at or above the configured threshold as automated', function (): void {
        $threshold = config()->integer('analytics.bot_detection.threshold');

        expect(BotSignalDetector::isAutomated($threshold))->toBeTrue()
            ->and(BotSignalDetector::isAutomated($threshold - 1))->toBeFalse();
    });
});
