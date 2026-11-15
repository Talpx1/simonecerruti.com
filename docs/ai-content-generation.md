# AI content generation

Every prose field of the admin panel has a **Generate with AI** button (sparkles icon) next to its label. It opens a modal with a preset prompt and a notes box. On submit, Claude writes the text and it replaces the content of the field, in the language of the active locale tab. The text is not saved until you save the form.

## Setup

The feature uses the Anthropic API through the [Laravel AI SDK](https://laravel.com/docs/ai-sdk) (`laravel/ai`).

1. In the Claude Console, link the organization to your Claude Max plan. Max includes monthly API credits ($100 for Max, $200 for Ultra). They expire every month and requests stop when they run out.
2. Create an API key in that organization and set `ANTHROPIC_API_KEY` in the `.env` of each environment (`DOTENV_STAGING` and `DOTENV_PRODUCTION` in production). `config/ai.php` reads it.
3. Nothing else is needed: the default provider is `anthropic` and the model is set on the agent (`App\Ai\Agents\ContentWriter`).

The API credits do not cover Claude Code, and a generation costs a few cents at most. If the credits run out, the modal shows "The AI credits are exhausted" and the field stays unchanged.

## The generation modal

| Part | Meaning |
| --- | --- |
| Prompt | The task for this field. It starts from the saved prompt of the field and can be edited for this run only. |
| Notes and raw material | Optional facts, bullets or a draft the AI must use. |
| Save this prompt as the default | Stores the edited prompt as the new default of the field. |

Besides the prompt and the notes, the request carries the current value of the field, a few sibling fields as context (for example the article title when you generate its summary), the language of the active locale and the maximum length of the field. Rich text fields are generated as HTML; the other fields as plain text. The result is cut at the maximum length of the field when it has one.

Generation runs synchronously while the modal is open, so a long article can take up to a minute.

## Prompts and style guide

Prompts live in the database (`App\Settings\AiSettings`, group `ai`) and are edited in the admin page **AI settings**:

- **Style guide**: general instructions sent with every request (voice, tone, rules). It is the system prompt of the agent.
- **Per-field prompts**: one box per field. A blank box means the default prompt, defined in `GeneratableFields::getDefaultPrompt()`.

## Authorization

The `generate-ai-content` and `manage-ai-settings` abilities (`App\Enums\Abilities`) are granted to every user who can access the admin panel. `GenerateFieldContent` and `SaveFieldPrompt` check them explicitly, so the rule also holds outside Filament. The prompt, the notes and the generated text are never written to the logs.

## Adding a generatable field

1. Add a case to `App\Enums\GeneratableFields` and fill its default prompt, subject, group, maximum length, rich text flag and context fields (state paths relative to the field).
2. Add the label to `lang/en/enums.php` and `lang/it/enums.php`.
3. Attach the button to the form field: `->hintAction(GenerateWithAiAction::make(GeneratableFields::YOUR_CASE))`. If the field already has a hint action, use `->hintActions([...])`.

The AI settings page lists the new field by itself.

## Code map

- `App\Filament\Components\GenerateWithAiAction`: Filament adapter (modal, state, notifications).
- `App\Actions\GenerateFieldContent`, `GetFieldPrompt`, `SaveFieldPrompt`: the logic, independent from Filament.
- `App\Ai\Agents\ContentWriter`: the agent (provider, model, timeout, structured output).
- `App\Data\FieldGenerationData`: input of the generation.
- `resources/views/ai/field-generation-prompt.blade.php`: the request built from the data.
