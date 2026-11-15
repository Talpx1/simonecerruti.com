<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\GeneratableFields;
use App\Settings\AiSettings;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetFieldPrompt {
    use AsAction;

    public function handle(GeneratableFields $field): string {
        $override = resolve(AiSettings::class)->prompts[$field->value] ?? null;

        return \is_string($override) && \trim($override) !== '' ? $override : $field->getDefaultPrompt();
    }
}
