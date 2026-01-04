<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasLocalizedDescription;
use App\Enums\Concerns\HasLocalizedLabel;
use App\Enums\Concerns\HasRandomPicker;
use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;

enum BlogArticleStatuses: string implements HasColor, HasDescription, HasLabel {
    use HasLocalizedDescription, HasLocalizedLabel, HasRandomPicker;

    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';
    case HIDDEN = 'hidden';

    /**
     * @return array<int, string>
     */
    public function getColor(): array {
        return match ($this) {
            self::DRAFT => Color::Yellow,
            self::PUBLISHED => Color::Green,
            self::ARCHIVED => Color::Slate,
            self::HIDDEN => Color::Gray,
        };
    }
}
