<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasLocalizedLabel;
use App\Enums\Concerns\HasRandomPicker;
use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

enum LeadReadStatus: string implements HasColor, HasIcon, HasLabel {
    use HasLocalizedLabel, HasRandomPicker;

    case UNREAD = 'unread';
    case READ = 'read';

    /**
     * @return array<int, string>
     */
    public function getColor(): array {
        return match ($this) {
            self::UNREAD => Color::Blue,
            self::READ => Color::Green,
        };
    }

    public function getIcon(): Heroicon {
        return match ($this) {
            self::UNREAD => Heroicon::OutlinedEnvelope,
            self::READ => Heroicon::OutlinedEnvelopeOpen,
        };
    }
}
