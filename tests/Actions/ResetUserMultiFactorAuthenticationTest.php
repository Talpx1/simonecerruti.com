<?php

declare(strict_types=1);

use App\Actions\ResetUserMultiFactorAuthentication;
use App\Models\User;

describe('reset', function (): void {
    it('removes every multi-factor method from the user', function (): void {
        $user = User::factory()->withEmailAuthentication()->create(['app_authentication_secret' => 'SECRET', 'app_authentication_recovery_codes' => ['code']]);

        expect($user->fresh())
            ->app_authentication_secret->toBe('SECRET')
            ->app_authentication_recovery_codes->toBe(['code'])
            ->has_email_authentication->toBeTrue();

        ResetUserMultiFactorAuthentication::run($user);

        expect($user->fresh())
            ->app_authentication_secret->toBeNull()
            ->app_authentication_recovery_codes->toBeNull()
            ->has_email_authentication->toBeFalse();
    });

    it('does not touch the other users', function (): void {
        $user = User::factory()->withEmailAuthentication()->create();
        $other_user = User::factory()->withEmailAuthentication()->create(['app_authentication_secret' => 'OTHER', 'app_authentication_recovery_codes' => ['other']]);

        ResetUserMultiFactorAuthentication::run($user);

        expect($other_user->fresh())
            ->app_authentication_secret->toBe('OTHER')
            ->app_authentication_recovery_codes->toBe(['other'])
            ->has_email_authentication->toBeTrue();
    });
});
