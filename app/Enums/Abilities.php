<?php

declare(strict_types=1);

namespace App\Enums;

enum Abilities: string {
    case GENERATE_AI_CONTENT = 'generate-ai-content';
    case MANAGE_AI_SETTINGS = 'manage-ai-settings';
}
