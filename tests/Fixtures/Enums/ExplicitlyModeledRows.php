<?php

declare(strict_types=1);

namespace Tests\Fixtures\Enums;

use App\Enums\Concerns\HasModel;
use App\Models\Lead;

enum ExplicitlyModeledRows: int {
    use HasModel;

    case FIRST = 1;

    public static function getModelClass(): string {
        return Lead::class;
    }
}
