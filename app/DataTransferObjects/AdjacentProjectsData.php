<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use App\Models\Project;

final readonly class AdjacentProjectsData {
    public function __construct(
        public ?Project $previous,
        public ?Project $next,
    ) {}
}
