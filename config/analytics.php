<?php

declare(strict_types=1);

return [
    'session_cookie_name' => 'vs_id',

    'visitor_cookie_name' => 'v_id',

    'session_window_minutes' => 30,

    'visitor_cookie_days' => 365,

    'skip_paths' => [
        'admin',
        'admin/*',
        '_debugbar*',
        'telescope*',
        'livewire/*',
        'up',
        'build/*',
        'storage/*',
        'sitemap*.xml',
        'robots.txt',
    ],

    'social_hosts' => [
        'instagram.com' => 'instagram',
        'www.instagram.com' => 'instagram',
        'facebook.com' => 'facebook',
        'l.facebook.com' => 'facebook',
        'm.facebook.com' => 'facebook',
        'linkedin.com' => 'linkedin',
        'www.linkedin.com' => 'linkedin',
        'lnkd.in' => 'linkedin',
        'twitter.com' => 'twitter',
        'x.com' => 'twitter',
        't.co' => 'twitter',
        'tiktok.com' => 'tiktok',
        'www.tiktok.com' => 'tiktok',
        'youtube.com' => 'youtube',
        'www.youtube.com' => 'youtube',
        'youtu.be' => 'youtube',
        'bsky.app' => 'bluesky',
    ],

    'search_hosts' => [
        'google.' => 'google',
        'bing.com' => 'bing',
        'duckduckgo.com' => 'duckduckgo',
        'yahoo.' => 'yahoo',
        'ecosia.org' => 'ecosia',
        'brave.com' => 'brave',
    ],

    /*
     * Header-consistency heuristic for visits whose User-Agent claims to be a
     * browser. Matomo's UA database only catches self-declared bots, so a
     * forged agent slips through as desktop/mobile. Genuine browsers always
     * send Accept, Accept-Language and Accept-Encoding, and modern engines add
     * Sec-Fetch-* metadata; each missing signal adds its weight to the visit's
     * bot_score. Once the score reaches `threshold` the visit is reclassified
     * as a bot. Defaults are conservative (a real modern browser scores 0;
     * only multiple concurring signals trip the threshold) — raise the weights
     * or lower the threshold to be more aggressive.
     */
    'bot_detection' => [
        'threshold' => 4,

        'weights' => [
            'missing_accept' => 2,
            'missing_accept_language' => 2,
            'missing_accept_encoding' => 1,
            'missing_sec_fetch' => 2,
        ],
    ],
];
