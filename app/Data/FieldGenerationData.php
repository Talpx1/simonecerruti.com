<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\GeneratableFields;
use Spatie\LaravelData\Data;

final class FieldGenerationData extends Data {
    /**
     * @param  array<string, string>  $context
     */
    public function __construct(
        public readonly GeneratableFields $field,
        public readonly string $prompt,
        public readonly ?string $notes,
        public readonly string $locale,
        public readonly ?string $current_value,
        public readonly array $context,
    ) {}
}
