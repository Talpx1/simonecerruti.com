<?php

declare(strict_types=1);

namespace App\Macros\Str;

use App\Macros\Concerns\Macro;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ReplacePlaceholdersMacro implements Macro {
    public static function register(): array {
        return [
            'replacePlaceholders',
            function (string $string, object|array|null $replacements = null): string {
                $regex = '/\{(\w+)\}/';
                \Safe\preg_match_all($regex, $string, $matches, PREG_SET_ORDER);

                $special_cases = [
                    'timestamp' => now()->timestamp,
                    'random' => uniqid(Str::random(8)),
                ];

                foreach ($matches as [$placeholder, $field]) {
                    $replacement = match (true) {
                        array_key_exists($field, $special_cases) => $special_cases[$field],
                        is_array($replacements) && array_key_exists($field, $replacements) => $replacements[$field],
                        is_object($replacements) && property_exists($replacements, $field) => $replacements->{$field},
                        is_object($replacements) && method_exists($replacements, $field) => $replacements->{$field}(),
                        $replacements instanceof Arrayable && array_key_exists($field, $replacements->toArray()) => $replacements->toArray()[$field],
                        function_exists($field) => $field(),
                        default => $placeholder
                    };

                    if (! is_string($replacement)) {
                        try {
                            // @phpstan-ignore argument.type
                            $replacement = strval($replacement);
                        } catch (InvalidArgumentException) {
                            $replacement = $placeholder;
                        }
                    }

                    $string = str_replace($placeholder, $replacement, $string);
                }

                return $string;
            },
        ];
    }
}
