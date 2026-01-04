<?php

declare(strict_types=1);

namespace App\Enums\Concerns;

trait HasLocalizedDescription {
    public function getDescription(): string {
        $slug = str(class_basename(__CLASS__))->snake()->lower()->toString();

        return __(strtolower("enums.{$slug}.{$this->name}.description"));
    }
}
