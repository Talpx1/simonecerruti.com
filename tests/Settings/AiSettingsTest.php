<?php

declare(strict_types=1);

use App\Settings\AiSettings;

it('seeds a style guide and no prompt overrides', function (): void {
    $settings = resolve(AiSettings::class);

    expect($settings->style_guide)->not->toBeEmpty()
        ->and($settings->prompts)->toBeEmpty();
});
