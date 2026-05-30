<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Robots meta directives. Each case's value is the exact token emitted in the
 * `<meta name="robots">` tag, so a set of cases joins into e.g. "noindex,nofollow".
 */
enum RobotsDirective: string {
    case NOINDEX = 'noindex';
    case NOFOLLOW = 'nofollow';
    case NOARCHIVE = 'noarchive';
    case NOSNIPPET = 'nosnippet';
    case NOIMAGEINDEX = 'noimageindex';
}
