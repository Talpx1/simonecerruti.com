<?php

declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Foundation\Events\DiagnosingHealth;
use Illuminate\Support\Facades\DB;

final class CheckDatabaseConnection {
    public function handle(DiagnosingHealth $event): void {
        DB::select('select 1');
    }
}
