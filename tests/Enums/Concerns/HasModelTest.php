<?php

declare(strict_types=1);

use App\Enums\Concerns\HasModel;
use App\Models\Lead;

/**
 * Fixture whose name singularizes to a real model (Leads -> App\Models\Lead),
 * exercising the guess path without an explicit getModelClass().
 */
enum Leads: int {
    use HasModel;

    case FIRST = 1;
}

/**
 * Fixture whose guessed model (App\Models\HasModelUnguessableFixture) does not exist.
 */
enum HasModelUnguessableFixture: int {
    use HasModel;

    case FIRST = 1;
}

it('guesses the model class from the enum name', function () {
    $lead = Lead::factory()->create();

    expect(Leads::FIRST->model())->toBeInstanceOf(Lead::class)
        ->and(Leads::FIRST->model()->id)->toBe($lead->id);
});

it('throws when the model class cannot be guessed', function () {
    expect(fn () => HasModelUnguessableFixture::FIRST->model())
        ->toThrow(Exception::class);
});
