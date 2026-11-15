<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\AdjacentProjectsData;
use App\Models\Project;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetAdjacentProjects {
    use AsAction;

    public function handle(Project $project): AdjacentProjectsData {
        /** @var list<string> $ordered_ids */
        $ordered_ids = Project::query()->featuredRanked()->pluck('id')->all();

        $position = \array_search($project->id, $ordered_ids, true);
        $total = \count($ordered_ids);

        if ($position === false || $total < 2) {
            return new AdjacentProjectsData(previous: null, next: null);
        }

        $previous_id = match (true) {
            $total === 2 => $position === 1 ? $ordered_ids[0] : null,
            default => $ordered_ids[($position - 1 + $total) % $total],
        };
        $next_id = match (true) {
            $total === 2 => $position === 0 ? $ordered_ids[1] : null,
            default => $ordered_ids[($position + 1) % $total],
        };

        $projects = Project::query()
            ->with(['tags', 'media'])
            ->whereIn('id', \array_filter([$previous_id, $next_id], static fn (?string $id): bool => $id !== null))
            ->whereKeyNot($project->id)
            ->get()
            ->keyBy('id');

        return new AdjacentProjectsData(
            previous: $previous_id === null ? null : $projects->get($previous_id),
            next: $next_id === null ? null : $projects->get($next_id),
        );
    }
}
