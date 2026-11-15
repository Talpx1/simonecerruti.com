<?php

declare(strict_types=1);

use App\Actions\SaveFieldPrompt;
use App\Enums\GeneratableFields;
use App\Models\User;
use App\Settings\AiSettings;
use Illuminate\Auth\Access\AuthorizationException;

it('saves the prompt override for the field', function (): void {
    $this->actingAs(User::factory()->admin()->create());

    SaveFieldPrompt::run(GeneratableFields::TAG_DESCRIPTION, 'Custom prompt');

    expect(resolve(AiSettings::class)->prompts)->toBe([GeneratableFields::TAG_DESCRIPTION->value => 'Custom prompt']);
});

it('keeps the overrides of the other fields', function (): void {
    $this->actingAs(User::factory()->admin()->create());

    SaveFieldPrompt::run(GeneratableFields::TAG_DESCRIPTION, 'A');
    SaveFieldPrompt::run(GeneratableFields::SEO_TITLE, 'B');

    expect(resolve(AiSettings::class)->prompts)->toBe([
        GeneratableFields::TAG_DESCRIPTION->value => 'A',
        GeneratableFields::SEO_TITLE->value => 'B',
    ]);
});

it('rejects users that cannot access the admin panel', function (): void {
    $this->actingAs(User::factory()->create());

    SaveFieldPrompt::run(GeneratableFields::TAG_DESCRIPTION, 'Custom prompt');
})->throws(AuthorizationException::class);

it('rejects guests', function (): void {
    SaveFieldPrompt::run(GeneratableFields::TAG_DESCRIPTION, 'Custom prompt');
})->throws(AuthorizationException::class);
