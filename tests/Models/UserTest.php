<?php

declare(strict_types=1);

use App\Models\User;
use Filament\Facades\Filament;

describe('attributes', function (): void {
    describe('app_authentication_secret', function (): void {
        it('is stored encrypted', function (): void {
            $user = User::factory()->withoutMfa()->create();

            expect($user->getAppAuthenticationSecret())->toBeNull()
                ->and($user->saveAppAuthenticationSecret('SECRET'))->toBeNull();

            $raw_secret = User::query()->toBase()->where('id', $user->id)->value('app_authentication_secret');

            expect($user->fresh()->getAppAuthenticationSecret())->toBe('SECRET')
                ->and($raw_secret)->not->toBe('SECRET');
        });

        it('is hidden from serialization', function (): void {
            $user = User::factory()->make(['app_authentication_secret' => 'SECRET']);

            expect($user->toArray())->not->toHaveKey('app_authentication_secret');
        });
    });

    describe('app_authentication_recovery_codes', function (): void {
        it('is stored encrypted', function (): void {
            $user = User::factory()->withoutMfa()->create();

            expect($user->getAppAuthenticationRecoveryCodes())->toBeNull()
                ->and($user->saveAppAuthenticationRecoveryCodes(['first-code', 'second-code']))->toBeNull();

            $raw_codes = User::query()->toBase()->where('id', $user->id)->value('app_authentication_recovery_codes');

            expect($user->fresh()->getAppAuthenticationRecoveryCodes())->toBe(['first-code', 'second-code'])
                ->and($raw_codes)->not->toContain('first-code');
        });

        it('is hidden from serialization', function (): void {
            $user = User::factory()->make(['app_authentication_recovery_codes' => ['code']]);

            expect($user->toArray())->not->toHaveKey('app_authentication_recovery_codes');
        });
    });

    describe('has_email_authentication', function (): void {
        it('is toggled on and off', function (): void {
            $user = User::factory()->withoutMfa()->create();

            expect($user->hasEmailAuthentication())->toBeFalse()
                ->and($user->toggleEmailAuthentication(true))->toBeNull()
                ->and($user->fresh()->hasEmailAuthentication())->toBeTrue()
                ->and($user->toggleEmailAuthentication(false))->toBeNull()
                ->and($user->fresh()->hasEmailAuthentication())->toBeFalse();
        });
    });
});

describe('accessors', function (): void {
    describe('initials', function (): void {
        it('derives up to two uppercase initials from the name', function (): void {
            expect(User::factory()->make(['name' => 'simone cerruti rossi'])->initials)->toBe('SC')
                ->and(User::factory()->make(['name' => 'Madonna'])->initials)->toBe('M');
        });
    });
});

describe('filament', function (): void {
    describe('canAccessPanel', function (): void {
        it('allows verified users with a company email', function (): void {
            $user = User::factory()->admin()->create();

            expect($user->canAccessPanel(Filament::getDefaultPanel()))->toBeTrue();
        });

        it('rejects users with a different email domain', function (): void {
            $user = User::factory()->create();

            expect($user->canAccessPanel(Filament::getDefaultPanel()))->toBeFalse();
        });

        it('rejects unverified company users', function (): void {
            $user = User::factory()->admin()->unverified()->create();

            expect($user->canAccessPanel(Filament::getDefaultPanel()))->toBeFalse();
        });
    });

    describe('multi-factor authentication', function (): void {
        it('is required on the admin panel with app and email providers', function (): void {
            $panel = Filament::getDefaultPanel();

            expect($panel->isMultiFactorAuthenticationRequired())->toBeTrue()
                ->and($panel->getMultiFactorAuthenticationProviders())->toHaveCount(2);
        });

        it('uses the email as the app authentication holder name', function (): void {
            $user = User::factory()->admin()->make();

            expect($user->getAppAuthenticationHolderName())->toBe($user->email);
        });
    });
});
