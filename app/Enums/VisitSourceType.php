<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\Collectable;
use App\Enums\Concerns\HasLocalizedLabel;
use App\Enums\Concerns\HasRandomPicker;
use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum VisitSourceType: string implements HasColor, HasLabel {
    use Collectable, HasLocalizedLabel, HasRandomPicker;

    case DIRECT = 'direct';
    case INTERNAL = 'internal';
    case UNKNOWN = 'unknown';

    /**
     * @return array<int, string>
     */
    public function getColor(): array {
        return match ($this) {
            self::DIRECT => Color::Slate,
            self::INTERNAL => Color::Gray,
            self::UNKNOWN => Color::Zinc,
        };
    }
}
