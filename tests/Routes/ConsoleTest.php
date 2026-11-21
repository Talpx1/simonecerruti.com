<?php

declare(strict_types=1);

use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;

use function Safe\file_get_contents;

/**
 * @return list<Event>
 */
function getScheduledEventsRunning(string $artisan_command): array {
    return collect(resolve(Schedule::class)->events())
        ->filter(fn (Event $event): bool => \str_contains((string) $event->command, $artisan_command))
        ->values()
        ->all();
}

describe('backup schedule', function (): void {
    it('schedules the backup cleanup, run and monitor every day', function (string $command, string $expression): void {
        $events = getScheduledEventsRunning($command);

        expect($events)->toHaveCount(1)
            ->and($events[0]->expression)->toBe($expression);
    })->with([
        'clean' => ['backup:clean', '0 1 * * *'],
        'run' => ['backup:run', '30 1 * * *'],
        'monitor' => ['backup:monitor', '0 3 * * *'],
    ]);

    it('runs only when backups are enabled', function (string $command, bool $is_enabled): void {
        config()->set('backup.enabled', $is_enabled);

        expect(getScheduledEventsRunning($command)[0]->filtersPass(app()))->toBe($is_enabled);
    })->with([
        'clean enabled' => ['backup:clean', true],
        'clean disabled' => ['backup:clean', false],
        'run enabled' => ['backup:run', true],
        'run disabled' => ['backup:run', false],
        'monitor enabled' => ['backup:monitor', true],
        'monitor disabled' => ['backup:monitor', false],
    ]);

    it('is enabled by default', function (): void {
        expect(config('backup.enabled'))->toBeTrue();
    });
});

describe('activity log cleanup', function (): void {
    it('runs daily without overlapping, in the application timezone', function (): void {
        $events = getScheduledEventsRunning('activitylog:clean');

        expect($events)->toHaveCount(1)
            ->and($events[0]->expression)->toBe('0 2 * * *')
            ->and($events[0]->withoutOverlapping)->toBeTrue()
            ->and($events[0]->timezone)->toBe(config('app.actual_timezone'))
            ->and($events[0]->command)->not->toContain('--days');
    });

    it('keeps 30 days of records unless the retention is overridden', function (): void {
        expect(config('activitylog.clean_after_days'))->toBe(30);
    });
});

it('imports the Schedule facade instead of relying on the global alias', function (): void {
    expect(file_get_contents(base_path('routes/console.php')))->toContain('use Illuminate\Support\Facades\Schedule;');
});
