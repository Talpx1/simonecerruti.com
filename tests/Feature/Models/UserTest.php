<?php

declare(strict_types=1);

use App\Models\User;
use Filament\Facades\Filament;

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
