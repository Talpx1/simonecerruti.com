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
        'llms.txt',
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

    /*
     * Pan interaction analytics (laravel/pan). `allowed_analytics` is the
     * allow-list of `data-pan` names recorded by the package; any name not
     * listed is silently dropped. `max_analytics` caps how many distinct
     * names are tracked.
     */
    'pan' => [
        'max_analytics' => 100,

        'allowed_analytics' => [
            'cta-nav-home',
            'cta-nav-about',
            'cta-nav-projects',
            'cta-nav-how_i_work',
            'cta-nav-contacts',
            'cta-nav-blog',
            'cta-social-linkedin-menu',
            'cta-social-instagram-menu',
            'cta-social-github-menu',
            'cta-social-bluesky-menu',
            'cta-social-x-menu',
            'cta-social-linkedin-footer',
            'cta-social-instagram-footer',
            'cta-social-github-footer',
            'cta-social-bluesky-footer',
            'cta-social-x-footer',
            'cta-social-linkedin-contacts',
            'cta-social-instagram-contacts',
            'cta-social-github-contacts',
            'cta-social-bluesky-contacts',
            'cta-social-x-contacts',
            'cta-hero-projects',
            'cta-hero-contacts',
            'cta-services-custom-software-development',
            'cta-services-area-2',
            'cta-services-area-3',
            'cta-services-contacts',
            'cta-services-projects',
            'cta-services-custom-software-development-consultation',
            'cta-services-custom-software-development-project',
            'cta-services-custom-software-development-case',
            'cta-services-custom-software-development-contacts',
            'cta-services-custom-software-development-back',
            'cta-services-web-development-consultation',
            'cta-services-web-development-project',
            'cta-services-web-development-case',
            'cta-services-web-development-contacts',
            'cta-services-web-development-back',
            'cta-services-consulting-and-seo-consultation',
            'cta-services-consulting-and-seo-project',
            'cta-services-consulting-and-seo-case',
            'cta-services-consulting-and-seo-contacts',
            'cta-services-consulting-and-seo-back',
            'cta-contact-email',
            'cta-contact-form',
            'cta-contacts-call',
            'cta-contacts-email',
            'cta-contacts-whatsapp',
            'cta-contacts-submit',
            'card-project-click',
            'card-blog-click',
            'section-impression-hero',
            'section-impression-services',
            'section-impression-services-hero',
            'section-impression-services-custom-software-development',
            'section-impression-services-web-development',
            'section-impression-services-consulting-and-seo',
            'section-impression-projects',
            'section-impression-cta',
            'section-impression-contacts',
        ],
    ],
];
