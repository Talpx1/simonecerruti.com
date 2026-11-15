<?php

declare(strict_types=1);

namespace Tests\Fixtures\Enums;

use App\Enums\Concerns\Collectable;
use App\Enums\Concerns\HasRandomPicker;

enum Fruits: string {
    use Collectable, HasRandomPicker;

    case APPLE = 'apple';
    case PEAR = 'pear';
    case PLUM = 'plum';
}
