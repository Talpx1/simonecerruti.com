<?php

declare(strict_types=1);

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration {
    public function up(): void {
        $this->migrator->add('ai.style_guide', implode(' ', [
            'You write website content for Simone Cerruti, an Italian freelance software developer and consultant.',
            'Voice: professional but warm, concrete and direct. Use the first person singular when the text describes the author\'s own work.',
            'Prefer specific facts over generic claims, short sentences and the active voice.',
            'Avoid hype, filler, buzzwords and exclamation marks.',
            'Never invent facts, figures, client names or quotes: use only what the provided material supports, and leave out what you cannot support.',
        ]));
        $this->migrator->add('ai.prompts', []);
    }
};
