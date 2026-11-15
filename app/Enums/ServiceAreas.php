<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\Collectable;
use App\Enums\Concerns\HasLocalizedLabel;
use App\Enums\Concerns\HasRandomPicker;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

enum ServiceAreas: string implements HasColor, HasIcon, HasLabel {
    use Collectable, HasLocalizedLabel, HasRandomPicker;

    case MANAGEMENT_ERP_CRM = 'management_erp_crm';
    case WEB_ECOMMERCE_PLATFORMS = 'web_ecommerce_platforms';
    case CONSULTING_AND_SEO = 'consulting_and_seo';

    public function getColor(): string {
        return match ($this) {
            self::MANAGEMENT_ERP_CRM => 'primary',
            self::WEB_ECOMMERCE_PLATFORMS => 'info',
            self::CONSULTING_AND_SEO => 'success',
        };
    }

    public function getIcon(): Heroicon {
        return match ($this) {
            self::MANAGEMENT_ERP_CRM => Heroicon::OutlinedCog6Tooth,
            self::WEB_ECOMMERCE_PLATFORMS => Heroicon::OutlinedGlobeAlt,
            self::CONSULTING_AND_SEO => Heroicon::OutlinedChartBar,
        };
    }

    public function getRouteName(): string {
        return "services.{$this->value}";
    }
}
