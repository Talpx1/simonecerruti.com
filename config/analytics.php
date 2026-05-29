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
];
