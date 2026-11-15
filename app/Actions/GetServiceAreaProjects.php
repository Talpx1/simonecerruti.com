<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\ServiceAreas;
use App\Models\Project;
use Illuminate\Database\Eloquent\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetServiceAreaProjects {
    use AsAction;

    /**
     * @return Collection<int, Project>
     */
    public function handle(ServiceAreas $area, int $limit): Collection {
        return Project::query()
            ->featuredRanked()
            ->whereRelation('tags', 'service', $area->value)
            ->with(['tags', 'client', 'media'])
            ->limit($limit)
            ->get();
    }
}
