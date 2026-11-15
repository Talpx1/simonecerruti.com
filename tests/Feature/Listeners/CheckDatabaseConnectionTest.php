<?php

declare(strict_types=1);

use App\Listeners\CheckDatabaseConnection;
use Illuminate\Foundation\Events\DiagnosingHealth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

describe('health route', function () {
    it('responds ok while the database is reachable', function () {
        $this->get('/up')->assertOk();
    });

    it('fails while the database is unreachable', function () {
        $default_connection = config()->string('database.default');

        config([
            'database.connections.unreachable' => [
                ...config()->array('database.connections.pgsql'),
                'host' => '127.0.0.1',
                'port' => 1,
            ],
            'database.default' => 'unreachable',
        ]);

        $this->get('/up')->assertServerError();

        // RefreshDatabase rolls back on the default connection: leave it where the transaction was opened.
        config(['database.default' => $default_connection]);
    });
});

it('listens to the health diagnosis event', function () {
    expect(Event::getListeners(DiagnosingHealth::class))->not->toBeEmpty();
});

it('runs a query against the default connection', function () {
    DB::enableQueryLog();

    new CheckDatabaseConnection()->handle(new DiagnosingHealth);

    expect(DB::getQueryLog())->toHaveCount(1)
        ->and(DB::getQueryLog()[0]['query'])->toBe('select 1');
});
