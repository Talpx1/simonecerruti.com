<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Schedule;
use Laravel\Telescope\Telescope;

Schedule::timezone(config()->string('app.actual_timezone'))->group(function (Illuminate\Console\Scheduling\Schedule $schedule) {
    $schedule->command('backup:clean')->when(fn (): bool => config('backup.enabled'))->dailyAt('01:00');
    $schedule->command('backup:run')->when(fn (): bool => config('backup.enabled'))->dailyAt('01:30');
    $schedule->command('backup:monitor')->when(fn (): bool => config('backup.enabled'))->dailyAt('03:00');

    if (app()->bound(Telescope::class)) {
        $schedule->command('telescope:prune')->dailyAt('23:00');
    }

    $schedule->command('activitylog:clean')->withoutOverlapping()->dailyAt('02:00');
    $schedule->command('app:generate-sitemap')->dailyAt('01:00');
});
