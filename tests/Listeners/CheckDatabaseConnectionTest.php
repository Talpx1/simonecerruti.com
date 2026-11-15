<?php

declare(strict_types=1);

use App\Listeners\CheckDatabaseConnection;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Events\DiagnosingHealth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

use function Pest\Laravel\get;

beforeEach(function (): void {
    $this->default_connection = config()->string('database.default');
});

afterEach(function (): void {
    config(['database.default' => $this->default_connection]);
});

describe('health route', function (): void {
    it('responds ok while the database is reachable', function (): void {
        get('/up')->assertOk();
    });

    it('fails while the database is unreachable', function (): void {
        switchToUnreachableDatabaseConnection();

        get('/up')->assertServerError();
    });
});

describe('listener', function (): void {
    it('listens to the health diagnosis event', function (): void {
        expect(Event::getListeners(DiagnosingHealth::class))->not->toBeEmpty();
    });

    it('runs a query against the default connection', function (): void {
        DB::enableQueryLog();
        DB::flushQueryLog();

        expect(DB::getQueryLog())->toBeEmpty();

        new CheckDatabaseConnection()->handle(new DiagnosingHealth);

        expect(DB::getQueryLog())->toHaveCount(1)
            ->and(DB::getQueryLog()[0]['query'])->toBe('select 1');
    });

    it('throws when the database is unreachable', function (): void {
        switchToUnreachableDatabaseConnection();

        expect(fn () => new CheckDatabaseConnection()->handle(new DiagnosingHealth))
            ->toThrow(QueryException::class, 'SQLSTATE');
    });
});
