<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Sleep;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in(__DIR__)
    ->beforeEach(function (): void {
        Mail::fake();
        Notification::fake();
        Sleep::fake();
        Storage::fake('local');
        App::setLocale(config()->string('app.locale'));
        Http::preventStrayRequests();
        Process::fake([]);
        Process::preventStrayProcesses();
        $this->freezeTime();
    })
    ->afterEach(function (): void {
        Str::createUuidsNormally();
    });

pest()->in('Browser')
    ->beforeAll(function (): void {
        $manifest = dirname(__DIR__).'/public/build/manifest.json';
        $newest_source = 0;

        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(dirname(__DIR__).'/resources', FilesystemIterator::SKIP_DOTS)) as $source) {
            $newest_source = max($newest_source, $source->getMTime());
        }

        $lock = fopen(dirname(__DIR__).'/storage/framework/vite-build.lock', 'c');

        flock($lock, LOCK_EX);

        if (! is_file($manifest) || filemtime($manifest) < $newest_source) {
            exec('npm run build 2>&1', $output, $exit_code);

            throw_if($exit_code !== 0, RuntimeException::class, implode("\n", $output));
        }

        flock($lock, LOCK_UN);
        fclose($lock);
    })
    ->beforeEach(function (): void {
        Vite::useHotFile(storage_path('framework/vite.hot'));
    });

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

// expect collection of models contains the provided collection of models
expect()->intercept(
    'toContain',
    fn ($value) => is_a($value, Collection::class) && $value->every(fn ($entry) => is_a($entry, Model::class)),
    fn (Collection $models) => expect($this->value->pluck('id'))->toContain(...$models->pluck('id')->toArray())
);

// expect the provided model to be the same model
expect()->intercept('toBe', Model::class, function (Model $expected): void {
    expect($expected->is($this->value))->toBeTrue(
        'Failed asserting that two models are the same record (same table, connection and key).'
    );
});

expect()->intercept('toEqual', Model::class, function (Model $expected): void {
    expect($expected->is($this->value))->toBeTrue(
        'Failed asserting that two models are the same record (same table, connection and key).'
    );
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

/**
 * Authenticate as a user allowed to access the Filament admin panel.
 */
function actingAsAdmin(): User {
    $user = User::factory()->create([
        'email' => fake()->unique()->userName().'@simonecerruti.com',
    ]);

    test()->actingAs($user);

    return $user;
}

/**
 * Run the given callback with the application locale temporarily switched.
 *
 * @template TReturn
 *
 * @param  Closure(): TReturn  $callback
 * @return TReturn
 */
function withLocale(string $locale, Closure $callback) {
    $original = app()->getLocale();

    app()->setLocale($locale);

    try {
        return $callback();
    } finally {
        app()->setLocale($original);
    }
}

/**
 * @return EloquentCollection<int, Activity>
 */
function getActivityLogsFor(Model $subject, ?string $event = null): EloquentCollection {
    return Activity::query()
        ->forSubject($subject)
        ->when($event !== null, fn (Builder $query): Builder => $query->forEvent($event))
        ->orderBy('id')
        ->get();
}

function getLastActivityLogsFor(Model $subject, ?string $event = null): ?Activity {
    return getActivityLogsFor($subject, $event)->last();
}

function assertNoActivityLogsFor(Model $subject, ?string $event = null): void {
    expect(getActivityLogsFor($subject, $event))->toBeEmpty();
}

function assertCountActivityLogsFor(Model $subject, int $count, ?string $event = null): void {
    expect(getActivityLogsFor($subject, $event))->toHaveCount($count);
}
