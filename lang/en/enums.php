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

    'service_areas' => [
        'management_erp_crm' => ['label' => 'Management software, ERP & CRM'],
        'web_ecommerce_platforms' => ['label' => 'Websites & e-commerce platforms'],
        'consulting_and_seo' => ['label' => 'Consulting & SEO'],
    ],

    'lead_read_status' => [
        'read' => ['label' => 'Letto'],
        'unread' => ['label' => 'Non letto'],
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

    'generatable_fields' => [
        'blog_article_title' => ['label' => 'Blog article title'],
        'blog_article_summary' => ['label' => 'Blog article summary'],
        'blog_article_content' => ['label' => 'Blog article content'],
        'project_title' => ['label' => 'Project title'],
        'project_short_description' => ['label' => 'Project short description'],
        'project_description' => ['label' => 'Project description'],
        'project_starting_point' => ['label' => 'Project starting point'],
        'project_goal' => ['label' => 'Project goal'],
        'project_outcome' => ['label' => 'Project outcome'],
        'project_feature_title' => ['label' => 'Project feature title'],
        'project_feature_description' => ['label' => 'Project feature description'],
        'project_home_summary' => ['label' => 'Project home summary'],
        'client_description' => ['label' => 'Client description'],
        'tag_description' => ['label' => 'Tag description'],
        'campaign_description' => ['label' => 'Campaign description'],
        'campaign_notes' => ['label' => 'Campaign notes'],
        'seo_title' => ['label' => 'Meta title'],
        'seo_description' => ['label' => 'Meta description'],
        'seo_og_title' => ['label' => 'Open Graph title'],
        'seo_og_description' => ['label' => 'Open Graph description'],
        'seo_twitter_title' => ['label' => 'Twitter title'],
        'seo_twitter_description' => ['label' => 'Twitter description'],
    ],
];
