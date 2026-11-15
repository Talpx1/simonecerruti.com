<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

it('never unguards models', function (): void {
    expect(Model::isUnguarded())->toBeFalse();
});

it('requires strong passwords by default', function (): void {
    $weak = Validator::make(['password' => 'short'], ['password' => Password::default()]);
    $strong = Validator::make(['password' => 'Str0ng!Passw0rd'], ['password' => Password::default()]);

    expect($weak->fails())->toBeTrue()
        ->and($strong->fails())->toBeFalse();
});
