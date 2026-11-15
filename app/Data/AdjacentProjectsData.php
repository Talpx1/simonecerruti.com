<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Project;
use Spatie\LaravelData\Data;

final class AdjacentProjectsData extends Data {
    public function __construct(
        public readonly ?Project $previous,
        public readonly ?Project $next,
    ) {}
}
