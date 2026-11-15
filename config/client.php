<?php

declare(strict_types=1);

return [

    'image' => [
        'disk' => 'public',
        'visibility' => 'public',
        'max_file_size_kb' => 1024 * 5, // 5MB
        'aspect_ratio' => '1:1',
        'resize_mode' => 'cover',
        'final_size_px' => 600,
        'quality' => 80,
        'optimize' => true,
        'accepted_mimes' => ['image/jpeg', 'image/png', 'image/webp'],
    ],

];
