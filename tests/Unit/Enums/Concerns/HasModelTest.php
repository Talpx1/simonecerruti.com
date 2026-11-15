<?php

declare(strict_types=1);

use App\Enums\Concerns\HasModel;
use App\Models\Tag;

/**
 * Fixture whose name singularizes to a real integer-keyed model (Tags -> App\Models\Tag),
 * exercising the guess path without an explicit getModelClass().
 */
enum Tags: int {
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
    $tag = Tag::query()->forceCreate(['id' => Tags::FIRST->value, 'name' => ['en' => fake()->word()]]);

    expect(Tags::FIRST->model())->toBeInstanceOf(Tag::class)
        ->and(Tags::FIRST->model()->id)->toBe($tag->id);
});

it('throws when the model class cannot be guessed', function () {
    expect(fn () => HasModelUnguessableFixture::FIRST->model())
        ->toThrow(Exception::class);
});
