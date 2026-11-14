<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Project;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetAdjacentProjects {
    use AsAction;

    /**
     * @return array{previous: Project, next: Project}
     */
    public function handle(Project $project): array {
        /** @var list<int> $ordered_ids */
        $ordered_ids = Project::query()->featuredRanked()->pluck('id')->all();

        if ($ordered_ids === []) {
            return ['previous' => $project, 'next' => $project];
        }

        $position = \array_search($project->id, $ordered_ids, true);
        $last_index = \count($ordered_ids) - 1;

        $previous_id = $position === false || $position === 0 ? $ordered_ids[$last_index] : $ordered_ids[$position - 1];
        $next_id = $position === false || $position === $last_index ? $ordered_ids[0] : $ordered_ids[$position + 1];

        $projects = Project::query()
            ->with(['tags', 'media'])
            ->whereIn('id', [$previous_id, $next_id])
            ->get()
            ->keyBy('id');

        return [
            'previous' => $projects->get($previous_id, $project),
            'next' => $projects->get($next_id, $project),
        ];
    }
}
