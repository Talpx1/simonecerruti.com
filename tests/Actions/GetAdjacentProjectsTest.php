<?php

declare(strict_types=1);

use App\Actions\GetAdjacentProjects;
use App\Models\Project;

describe('neighbours', function (): void {
    it('follows the public list order', function (): void {
        [$featured, $newest, $oldest] = createRankedProjects();

        $result = runWithLocale('en', fn (): mixed => GetAdjacentProjects::run($newest));

        expect($result->previous)->toBe($featured)
            ->and($result->next)->toBe($oldest);
    });

    it('wraps around at both ends', function (): void {
        [$featured, $newest, $oldest] = createRankedProjects();

        $first = runWithLocale('en', fn (): mixed => GetAdjacentProjects::run($featured));
        $last = runWithLocale('en', fn (): mixed => GetAdjacentProjects::run($oldest));

        expect($first->previous)->toBe($oldest)
            ->and($first->next)->toBe($newest)
            ->and($last->previous)->toBe($newest)
            ->and($last->next)->toBe($featured);
    });

    it('never returns the current project with three or more projects', function (int $position): void {
        $project = createRankedProjects()[$position];

        $result = runWithLocale('en', fn (): mixed => GetAdjacentProjects::run($project));

        expect($result->previous)->not->toBeNull()
            ->and($result->next)->not->toBeNull()
            ->and($result->previous->is($project))->toBeFalse()
            ->and($result->next->is($project))->toBeFalse();
    })->with([0, 1, 2]);

    it('returns null on both sides when it is the only project', function (): void {
        $project = Project::factory()->create();

        $result = runWithLocale('en', fn (): mixed => GetAdjacentProjects::run($project));

        expect($result->previous)->toBeNull()
            ->and($result->next)->toBeNull();
    });

    it('returns null on both sides when the project is not in the ranked list', function (): void {
        createRankedProjects();
        $unpublished = Project::factory()->unpublished()->create();

        $result = runWithLocale('en', fn (): mixed => GetAdjacentProjects::run($unpublished));

        expect($result->previous)->toBeNull()
            ->and($result->next)->toBeNull();
    });

    it('does not wrap around when there are only two projects', function (): void {
        $first = Project::factory()->featured()->create();
        $last = Project::factory()->create(['created_at' => now()->subDay()]);

        $first_result = runWithLocale('en', fn (): mixed => GetAdjacentProjects::run($first));
        $last_result = runWithLocale('en', fn (): mixed => GetAdjacentProjects::run($last));

        expect($first_result->previous)->toBeNull()
            ->and($first_result->next->is($last))->toBeTrue()
            ->and($last_result->previous->is($first))->toBeTrue()
            ->and($last_result->next)->toBeNull();
    });

    it('ignores unpublished projects', function (): void {
        [$featured, $newest] = createRankedProjects();
        Project::factory()->unpublished()->create(['created_at' => now()->subDays(2)]);

        $result = runWithLocale('en', fn (): mixed => GetAdjacentProjects::run($newest));

        expect($result->previous)->toBe($featured);
    });

    it('eager loads tags and media on the neighbours', function (): void {
        [, $newest] = createRankedProjects();

        $result = runWithLocale('en', fn (): mixed => GetAdjacentProjects::run($newest));

        expect($result->previous->relationLoaded('tags'))->toBeTrue()
            ->and($result->previous->relationLoaded('media'))->toBeTrue()
            ->and($result->next->relationLoaded('tags'))->toBeTrue()
            ->and($result->next->relationLoaded('media'))->toBeTrue();
    });
});
