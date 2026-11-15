<?php

declare(strict_types=1);

use App\Models\Lead;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\Fixtures\Enums\ExplicitlyModeledRows;
use Tests\Fixtures\Enums\GuessedModels;
use Tests\Fixtures\Enums\UnguessableModels;

describe('model', function (): void {
    it('uses the model class declared by the enum', function (): void {
        $lead = Lead::factory()->create(['id' => ExplicitlyModeledRows::FIRST->value]);

        expect(ExplicitlyModeledRows::FIRST->model())->toBeInstanceOf(Lead::class)
            ->and(ExplicitlyModeledRows::FIRST->model()->id)->toBe($lead->id);
    });

    it('guesses the model class from the enum name', function (): void {
        if (! \class_exists('App\Models\GuessedModel')) {
            \class_alias(Lead::class, 'App\Models\GuessedModel');
        }

        $lead = Lead::factory()->create(['id' => GuessedModels::FIRST->value]);

        expect(GuessedModels::FIRST->model())->toBeInstanceOf(Lead::class)
            ->and(GuessedModels::FIRST->model()->id)->toBe($lead->id);
    });

    it('throws when the model class cannot be guessed', function (): void {
        expect(fn () => UnguessableModels::FIRST->model())
            ->toThrow(Exception::class, 'could not be guessed');
    });

    it('fails when the related row does not exist', function (): void {
        expect(Lead::query()->count())->toBe(0)
            ->and(fn () => ExplicitlyModeledRows::FIRST->model())
            ->toThrow(ModelNotFoundException::class);
    });
});
