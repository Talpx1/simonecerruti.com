<?php

declare(strict_types=1);

return [
    'blog_article_statuses' => [
        'draft' => [
            'label' => 'Draft',
            'description' => 'The article is not yet ready for publication. It is not displayed anywhere on the website, is not accessible via link, and is not visible to search engines.',
        ],
        'published' => [
            'label' => 'Published',
            'description' => 'The article is public and visible on the website and to search engines.',
        ],
        'archived' => [
            'label' => 'Archived',
            'description' => 'The article is not displayed on the website (searches, article list, etc.). It can still be accessed via link and is visible to search engines.',
        ],
        'hidden' => [
            'label' => 'Hidden',
            'description' => 'The article is not displayed on the website (searches, article list, etc.), is not accessible via link, and is not visible to search engines.',
        ],
    ],

    'lead_read_status' => [
        'read' => ['label' => 'Letto'],
        'unread' => ['label' => 'Non letto'],
    ],

    'blog_categories' => [
        'practical' => ['label' => 'practical'],
        'technical' => ['label' => 'technical'],
    ],

    'visit_source_type' => [
        'direct' => ['label' => 'Direct'],
        'internal' => ['label' => 'Internal'],
        'unknown' => ['label' => 'Unknown'],
    ],

    'visit_medium_type' => [
        'social' => ['label' => 'Social'],
        'email' => ['label' => 'Email'],
        'organic' => ['label' => 'Organic'],
        'paid' => ['label' => 'Paid'],
        'display' => ['label' => 'Display'],
        'referral' => ['label' => 'Referral'],
        'affiliate' => ['label' => 'Affiliate'],
        'print' => ['label' => 'Print'],
        'physical' => ['label' => 'Physical'],
    ],

    'device_type' => [
        'mobile' => ['label' => 'Mobile'],
        'tablet' => ['label' => 'Tablet'],
        'desktop' => ['label' => 'Desktop'],
        'bot' => ['label' => 'Bot'],
        'unknown' => ['label' => 'Unknown'],
    ],
];
