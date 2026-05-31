<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

/**
 * Twitter Card types. The value is the exact token emitted in the
 * `<meta name="twitter:card">` tag.
 */
enum TwitterCard: string implements HasLabel {
    case SUMMARY = 'summary';
    case SUMMARY_LARGE_IMAGE = 'summary_large_image';
    case APP = 'app';
    case PLAYER = 'player';

    /**
     * Admin label: a human-readable form of the card type (e.g. "Summary Large Image").
     */
    public function getLabel(): string {
        return Str::headline($this->value);
    }
}
