<?php

declare(strict_types=1);

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\App;

test('blocks a non-local environment', function (): void {
    App::detectEnvironment(fn (): string => 'production');

    expect(fn () => (new DatabaseSeeder)->run())->toThrow(RuntimeException::class)
        ->and(User::query()->count())->toBe(0);
});

test('does not create the local admin user in the testing environment', function (): void {
    (new DatabaseSeeder)->run();

    expect(User::query()->count())->toBe(0);
});

test('creates the verified local admin user in the local environment', function (): void {
    App::detectEnvironment(fn (): string => 'local');

    (new DatabaseSeeder)->run();

    expect(User::query()->where('email', 'hello@simonecerruti.com')->firstOrFail()->hasVerifiedEmail())->toBeTrue();
});
