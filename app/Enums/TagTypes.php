<?php

declare(strict_types=1);

namespace App\Enums;

enum TagTypes: string {
    case TAG = 'tag';
    case BLOG_CATEGORY = 'blog_category';
    case TECHNOLOGY = 'technology';
}
