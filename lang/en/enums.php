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
];
