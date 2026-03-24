<?php

declare(strict_types=1);

return [
    'featured_image' => [
        'disk' => 'public',
        'visibility' => 'public',
        'max_file_size_kb' => 1024 * 5, // 5MB
        'aspect_ratio' => '16:9',
        'resize_mode' => 'cover',
        'final_width_px' => 1920,
        'final_height_px' => 1080,
        'quality' => 80,
        'optimize' => true,
    ],
];
