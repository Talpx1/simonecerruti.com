<?php

declare(strict_types=1);

$chrome_user_agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36';
$safari_user_agent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 14_4) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.4 Safari/605.1.15';

dataset('bot_header_scores', [
    'full browser request scores zero' => [
        'text/html', 'it-IT,it;q=0.9', 'gzip, deflate, br', 'none', $chrome_user_agent, 0,
    ],
    'missing Accept-Language alone stays below threshold' => [
        'text/html', null, 'gzip, deflate, br', 'none', $chrome_user_agent, 2,
    ],
    'missing Sec-Fetch alone on a capable browser scores its weight' => [
        'text/html', 'it-IT,it;q=0.9', 'gzip, deflate, br', null, $chrome_user_agent, 2,
    ],
    'missing Accept-Language and Sec-Fetch trips the threshold' => [
        'text/html', null, 'gzip, deflate, br', null, $chrome_user_agent, 4,
    ],
    'bare scraper claiming Chrome scores high' => [
        null, null, null, null, $chrome_user_agent, 7,
    ],
    'blank (whitespace) headers count as missing' => [
        '   ', '   ', 'gzip', 'none', $chrome_user_agent, 4,
    ],
    'missing Sec-Fetch is not penalised for a non Sec-Fetch browser' => [
        'text/html', 'it-IT', 'gzip', null, $safari_user_agent, 0,
    ],
]);
