<?php

declare(strict_types=1);

use App\Models\User;

use function Pest\Laravel\artisan;

describe('users:reset-mfa', function (): void {
    it('resets the multi-factor methods of the user with the given email', function (): void {
        $user = User::factory()->withEmailAuthentication()->create();

        expect($user->fresh()->has_email_authentication)->toBeTrue();

        artisan('users:reset-mfa', ['email' => $user->email])
            ->expectsOutputToContain("Multi-factor authentication reset for {$user->email}.")
            ->assertExitCode(0);

        expect($user->fresh()->has_email_authentication)->toBeFalse();
    });

    it('finds the user regardless of the email casing', function (): void {
        $user = User::factory()->withEmailAuthentication()->create(['email' => 'ada@example.com']);

        artisan('users:reset-mfa', ['email' => 'ADA@Example.COM'])
            ->expectsOutputToContain('Multi-factor authentication reset for ada@example.com.')
            ->assertSuccessful();

        expect($user->fresh()->has_email_authentication)->toBeFalse();
    });

    it('fails without resetting anything when the user does not exist', function (): void {
        $user = User::factory()->withEmailAuthentication()->create();

        artisan('users:reset-mfa', ['email' => 'missing@example.com'])
            ->expectsOutputToContain('User not found.')
            ->assertFailed();

        expect($user->fresh()->has_email_authentication)->toBeTrue();
    });
});
