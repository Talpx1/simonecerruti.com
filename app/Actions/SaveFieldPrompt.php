<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\GeneratableFields;
use App\Settings\AiSettings;
use Lorisleiva\Actions\Concerns\AsAction;

final class SaveFieldPrompt {
    use AsAction;

    public function handle(GeneratableFields $field, string $prompt): void {
        $settings = resolve(AiSettings::class);
        $prompts = $settings->prompts;
        $prompts[$field->value] = $prompt;
        $settings->prompts = $prompts;
        $settings->save();
    }
}
