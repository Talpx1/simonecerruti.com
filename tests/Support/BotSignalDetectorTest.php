<?php

declare(strict_types=1);

use App\Support\BotSignalDetector;

const CHROME_UA = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36';

const SAFARI_UA = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 14_4) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.4 Safari/605.1.15';

dataset('header_scores', [
    'full browser request scores zero' => [
        'text/html', 'it-IT,it;q=0.9', 'gzip, deflate, br', 'none', CHROME_UA, 0,
    ],
    'missing Accept-Language alone stays below threshold' => [
        'text/html', null, 'gzip, deflate, br', 'none', CHROME_UA, 2,
    ],
    'missing Sec-Fetch alone on a capable browser scores its weight' => [
        'text/html', 'it-IT,it;q=0.9', 'gzip, deflate, br', null, CHROME_UA, 2,
    ],
    'missing Accept-Language and Sec-Fetch trips the threshold' => [
        'text/html', null, 'gzip, deflate, br', null, CHROME_UA, 4,
    ],
    'bare scraper claiming Chrome scores high' => [
        null, null, null, null, CHROME_UA, 7,
    ],
    'blank (whitespace) headers count as missing' => [
        '   ', '   ', 'gzip', 'none', CHROME_UA, 4,
    ],
    'missing Sec-Fetch is not penalised for a non Sec-Fetch browser' => [
        'text/html', 'it-IT', 'gzip', null, SAFARI_UA, 0,
    ],
]);

it('scores request headers for automation signals', function (
    ?string $accept,
    ?string $accept_language,
    ?string $accept_encoding,
    ?string $sec_fetch_site,
    string $user_agent,
    int $expected,
) {
    expect(BotSignalDetector::score($accept, $accept_language, $accept_encoding, $sec_fetch_site, $user_agent))
        ->toBe($expected);
})->with('header_scores');

it('flags a score at or above the configured threshold as automated', function () {
    $threshold = config()->integer('analytics.bot_detection.threshold');

    expect(BotSignalDetector::isAutomated($threshold))->toBeTrue()
        ->and(BotSignalDetector::isAutomated($threshold - 1))->toBeFalse();
});
