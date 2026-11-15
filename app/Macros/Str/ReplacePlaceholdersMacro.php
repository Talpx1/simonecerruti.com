<?php

declare(strict_types=1);

namespace App\Macros\Str;

use App\Macros\Contracts\Macro;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;
use InvalidArgumentException;

final class ReplacePlaceholdersMacro extends Macro {
    protected static function macroable(): string {
        return Str::class;
    }

    protected static function name(): string {
        return 'replacePlaceholders';
    }

    /** @param  object|array<array-key, mixed>|null  $replacements */
    public static function handle(string $string, object|array|null $replacements = null, bool $strip_unmatched = false): string {
        \Safe\preg_match_all('/\{(\w+)\}/', $string, $matches, PREG_SET_ORDER);

        $special_cases = [
            'timestamp' => now()->timestamp,
            'random' => uniqid(Str::random(8)),
        ];

        $arrayable = $replacements instanceof Arrayable ? $replacements->toArray() : [];

        foreach ($matches as [$placeholder, $field]) {
            $replacement = match (true) {
                \array_key_exists($field, $special_cases) => $special_cases[$field],
                \is_array($replacements) && \array_key_exists($field, $replacements) => $replacements[$field],
                \is_object($replacements) && \property_exists($replacements, $field) => $replacements->{$field},
                \is_object($replacements) && \method_exists($replacements, $field) => $replacements->{$field}(),
                \array_key_exists($field, $arrayable) => $arrayable[$field],
                \function_exists($field) => $field(),
                default => $strip_unmatched ? '' : $placeholder,
            };

            if (! \is_string($replacement)) {
                try {
                    // @phpstan-ignore argument.type
                    $replacement = \strval($replacement);
                } catch (InvalidArgumentException) {
                    $replacement = $placeholder;
                }
            }

            $string = \str_replace($placeholder, $replacement, $string);
        }

        return $string;
    }
}
