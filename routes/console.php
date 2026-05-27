<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Schedule;
use Laravel\Telescope\Telescope;

Schedule::timezone(config()->string('app.actual_timezone'))->group(function (Illuminate\Console\Scheduling\Schedule $schedule) {
    if (config()->boolean('backup.enabled')) {
        $schedule->command('backup:clean')->dailyAt('01:00');
        $schedule->command('backup:run')->dailyAt('22:00');
        $schedule->command('backup:monitor')->dailyAt('03:00');
    }

    if (app()->bound(Telescope::class)) {
        $schedule->command('telescope:prune')->dailyAt('23:00');
    }

    $schedule->command('activitylog:clean --days=30')->dailyAt('00:00');
    $schedule->command('app:generate-sitemap')->dailyAt('01:00');
});
