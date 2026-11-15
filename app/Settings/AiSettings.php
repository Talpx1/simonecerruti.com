<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

final class AiSettings extends Settings {
    public string $style_guide;

    /** @var array<string, string> */
    public array $prompts;

    public static function group(): string {
        return 'ai';
    }
}
