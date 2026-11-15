<?php

declare(strict_types=1);

namespace Tests\Fixtures\Enums;

use App\Enums\Concerns\HasModel;

enum UnguessableModels: int {
    use HasModel;

    case FIRST = 1;
}
