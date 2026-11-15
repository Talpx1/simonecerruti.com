<?php

declare(strict_types=1);

use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;

describe('backup schedule', function (): void {
    it('schedules the backup cleanup, run and monitor every day', function (string $command, string $expression): void {
        $events = collect(resolve(Schedule::class)->events())
            ->filter(fn (Event $event): bool => \str_contains((string) $event->command, $command));

        expect($events)->toHaveCount(1)
            ->and($events->first()->expression)->toBe($expression);
    })->with([
        'clean' => ['backup:clean', '0 1 * * *'],
        'run' => ['backup:run', '30 1 * * *'],
        'monitor' => ['backup:monitor', '0 3 * * *'],
    ]);
});
