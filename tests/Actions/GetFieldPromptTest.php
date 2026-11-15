<?php

declare(strict_types=1);

use App\Actions\GetFieldPrompt;
use App\Enums\GeneratableFields;
use App\Settings\AiSettings;

it('returns the default prompt when there is no override', function (): void {
    expect(GetFieldPrompt::run(GeneratableFields::SEO_TITLE))->toBe(GeneratableFields::SEO_TITLE->getDefaultPrompt());
});

it('returns the saved override', function (): void {
    $settings = resolve(AiSettings::class);
    $settings->prompts = [GeneratableFields::SEO_TITLE->value => 'Custom prompt'];
    $settings->save();

    expect(GetFieldPrompt::run(GeneratableFields::SEO_TITLE))->toBe('Custom prompt');
});

it('falls back to the default when the override is blank', function (): void {
    $settings = resolve(AiSettings::class);
    $settings->prompts = [GeneratableFields::SEO_TITLE->value => '   '];
    $settings->save();

    expect(GetFieldPrompt::run(GeneratableFields::SEO_TITLE))->toBe(GeneratableFields::SEO_TITLE->getDefaultPrompt());
});
