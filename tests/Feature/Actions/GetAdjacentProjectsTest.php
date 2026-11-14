<?php

declare(strict_types=1);

use App\Actions\GetAdjacentProjects;
use App\Models\Project;

/**
 * @return array{0: Project, 1: Project, 2: Project}
 */
function createRankedProjects(): array {
    return [
        Project::factory()->featured()->create(['created_at' => now()->subDays(5)]),
        Project::factory()->create(['created_at' => now()->subDay()]),
        Project::factory()->create(['created_at' => now()->subDays(10)]),
    ];
}

describe('neighbours', function () {
    it('follows the public list order', function () {
        [$featured, $newest, $oldest] = createRankedProjects();

        $result = withLocale('en', fn () => GetAdjacentProjects::run($newest));

        expect($result['previous'])->toBe($featured)
            ->and($result['next'])->toBe($oldest);
    });

    it('wraps around at both ends', function () {
        [$featured, $newest, $oldest] = createRankedProjects();

        $first = withLocale('en', fn () => GetAdjacentProjects::run($featured));
        $last = withLocale('en', fn () => GetAdjacentProjects::run($oldest));

        expect($first['previous'])->toBe($oldest)
            ->and($first['next'])->toBe($newest)
            ->and($last['previous'])->toBe($newest)
            ->and($last['next'])->toBe($featured);
    });

    it('returns the same project as previous and next when it is the only one', function () {
        $project = Project::factory()->create();

        $result = withLocale('en', fn () => GetAdjacentProjects::run($project));

        expect($result['previous'])->toBe($project)
            ->and($result['next'])->toBe($project);
    });

    it('ignores unpublished projects', function () {
        [$featured, $newest] = createRankedProjects();
        Project::factory()->unpublished()->create(['created_at' => now()->subDays(2)]);

        $result = withLocale('en', fn () => GetAdjacentProjects::run($newest));

        expect($result['previous'])->toBe($featured);
    });

    it('eager loads tags and media on the neighbours', function () {
        [, $newest] = createRankedProjects();

        $result = withLocale('en', fn () => GetAdjacentProjects::run($newest));

        expect($result['previous']->relationLoaded('tags'))->toBeTrue()
            ->and($result['previous']->relationLoaded('media'))->toBeTrue()
            ->and($result['next']->relationLoaded('tags'))->toBeTrue()
            ->and($result['next']->relationLoaded('media'))->toBeTrue();
    });
});
