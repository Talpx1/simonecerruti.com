<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Twitter Card types. The value is the exact token emitted in the
 * `<meta name="twitter:card">` tag.
 */
enum TwitterCard: string {
    case SUMMARY = 'summary';
    case SUMMARY_LARGE_IMAGE = 'summary_large_image';
    case APP = 'app';
    case PLAYER = 'player';
}
