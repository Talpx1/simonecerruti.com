<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\Collectable;
use App\Enums\Concerns\HasLocalizedLabel;
use App\Enums\Concerns\HasRandomPicker;
use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum VisitMediumType: string implements HasColor, HasLabel {
    use Collectable, HasLocalizedLabel, HasRandomPicker;

    case SOCIAL = 'social';
    case EMAIL = 'email';
    case ORGANIC = 'organic';
    case PAID = 'paid';
    case DISPLAY = 'display';
    case REFERRAL = 'referral';
    case AFFILIATE = 'affiliate';
    case PRINT = 'print';
    case PHYSICAL = 'physical';

    /**
     * @return array<int, string>
     */
    public function getColor(): array {
        return match ($this) {
            self::SOCIAL => Color::Pink,
            self::EMAIL => Color::Indigo,
            self::ORGANIC => Color::Blue,
            self::PAID => Color::Emerald,
            self::DISPLAY => Color::Purple,
            self::REFERRAL => Color::Amber,
            self::AFFILIATE => Color::Lime,
            self::PRINT => Color::Orange,
            self::PHYSICAL => Color::Stone,
        };
    }
}
