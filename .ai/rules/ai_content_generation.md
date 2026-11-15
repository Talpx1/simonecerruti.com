---
paths:
    - app/Filament/**
    - app/Enums/GeneratableFields.php
    - database/migrations/**
---

# AI content generation

- Every user-fillable prose field that AI can generate (a title, summary, description, body, note, SEO text or similar) must be wired to the AI generation flow in the same change that adds it. Do not ship such a field without it.
- Wiring means all of the following:
    - Add a case to `App\Enums\GeneratableFields`, with its label (`lang/en/enums.php` and `lang/it/enums.php`), group label, subject, default prompt, rich-text flag, max characters (mirror the field's `maxLength`) and context fields.
    - Attach `->hintAction(GenerateWithAiAction::make(GeneratableFields::<CASE>))` to the Filament field. If the field already has a hint action, use `->hintActions([...])`.
    - Add the Italian translation of every new key to `lang/it.json`.
    - Cover the new case in the existing tests (`tests/Enums/GeneratableFieldsTest.php`, `tests/Filament/Components/GenerateWithAiActionTest.php`) and update `docs/ai-content-generation.md`.
- Do not wire fields that are not prose: slugs, URLs, names used as identifiers, enums, numbers, dates and flags.
- Removing or renaming a prose field means removing or renaming its `GeneratableFields` case and its hint action.
