<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\Collectable;
use App\Enums\Concerns\HasLocalizedLabel;
use App\Enums\Concerns\HasRandomPicker;
use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum DeviceType: string implements HasColor, HasLabel {
    use Collectable, HasLocalizedLabel, HasRandomPicker;

    case MOBILE = 'mobile';
    case TABLET = 'tablet';
    case DESKTOP = 'desktop';
    case BOT = 'bot';
    case UNKNOWN = 'unknown';

    /**
     * @return array<int, string>
     */
    public function getColor(): array {
        return match ($this) {
            self::MOBILE => Color::Sky,
            self::TABLET => Color::Cyan,
            self::DESKTOP => Color::Emerald,
            self::BOT => Color::Rose,
            self::UNKNOWN => Color::Zinc,
        };
    }
}
