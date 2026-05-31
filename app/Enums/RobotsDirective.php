<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

/**
 * Robots meta directives. Each case's value is the exact token emitted in the
 * `<meta name="robots">` tag, so a set of cases joins into e.g. "noindex,nofollow".
 */
enum RobotsDirective: string implements HasLabel {
    case NOINDEX = 'noindex';
    case NOFOLLOW = 'nofollow';
    case NOARCHIVE = 'noarchive';
    case NOSNIPPET = 'nosnippet';
    case NOIMAGEINDEX = 'noimageindex';

    /**
     * Admin label: the directive token itself (e.g. "noindex"), matching the
     * exact value emitted in the robots meta tag.
     */
    public function getLabel(): string {
        return $this->value;
    }
}
