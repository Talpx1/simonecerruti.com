<?php

declare(strict_types=1);

namespace App\Enums\Concerns;

use Illuminate\Support\Collection;

trait Collectable {
    /**
     * @return Collection<int, static>
     */
    public static function collect(): Collection {
        return collect(static::cases());
    }
}
