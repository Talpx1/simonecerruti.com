<?php

declare(strict_types=1);

use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\DB;

describe('canAccessPanel', function () {
    beforeEach(function () {
        $this->panel = Filament::getDefaultPanel();
    });

    it('allows verified users with a company email', function () {
        $user = User::factory()->create(['email' => 'simone@simonecerruti.com']);

        expect($user->canAccessPanel($this->panel))->toBeTrue();
    });

    it('rejects users with a different email domain', function () {
        $user = User::factory()->create(['email' => 'someone@gmail.com']);

        expect($user->canAccessPanel($this->panel))->toBeFalse();
    });

    it('rejects unverified company users', function () {
        $user = User::factory()->unverified()->create(['email' => 'simone@simonecerruti.com']);

        expect($user->canAccessPanel($this->panel))->toBeFalse();
    });
});

it('derives up to two uppercase initials from the name', function () {
    expect(User::factory()->make(['name' => 'simone cerruti rossi'])->initials)->toBe('SC')
        ->and(User::factory()->make(['name' => 'Madonna'])->initials)->toBe('M');
});

describe('multi-factor authentication', function () {
    it('requires multi-factor authentication on the admin panel with app and email providers', function () {
        $panel = Filament::getDefaultPanel();

        expect($panel->isMultiFactorAuthenticationRequired())->toBeTrue()
            ->and($panel->getMultiFactorAuthenticationProviders())->toHaveCount(2);
    });

    it('stores the app authentication secret and recovery codes encrypted', function () {
        $user = User::factory()->create();

        $user->saveAppAuthenticationSecret('SECRET');
        $user->saveAppAuthenticationRecoveryCodes(['first-code', 'second-code']);

        $raw = DB::table('users')->where('id', $user->id)->first();

        expect($user->fresh())
            ->getAppAuthenticationSecret()->toBe('SECRET')
            ->getAppAuthenticationRecoveryCodes()->toBe(['first-code', 'second-code'])
            ->and($raw->app_authentication_secret)->not->toBe('SECRET')
            ->and($raw->app_authentication_recovery_codes)->not->toContain('first-code');
    });

    it('toggles the email authentication', function () {
        $user = User::factory()->create();

        expect($user->hasEmailAuthentication())->toBeFalse();

        $user->toggleEmailAuthentication(true);

        expect($user->fresh()->hasEmailAuthentication())->toBeTrue();
    });

    it('uses the email as the app authentication holder name', function () {
        $user = User::factory()->make(['email' => 'simone@simonecerruti.com']);

        expect($user->getAppAuthenticationHolderName())->toBe('simone@simonecerruti.com');
    });

    it('hides the multi-factor secrets from serialization', function () {
        $user = User::factory()->make(['app_authentication_secret' => 'SECRET', 'app_authentication_recovery_codes' => ['code']]);

        expect($user->toArray())->not->toHaveKeys(['app_authentication_secret', 'app_authentication_recovery_codes']);
    });
});
