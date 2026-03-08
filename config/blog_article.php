<?php

declare(strict_types=1);

return [
    'featured_image' => [
        'disk' => 'public',
        'visibility' => 'public',
        'path' => 'blog_articles/{id}',
        'max_file_size_kb' => 1024 * 2, // 2MB
        'aspect_rateo' => '16:9',
        'final_width_px' => 1920,
        'final_height_px' => 1080,
    ],
];
