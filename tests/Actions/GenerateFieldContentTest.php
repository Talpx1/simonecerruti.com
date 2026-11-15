<?php

declare(strict_types=1);

use App\Actions\GenerateFieldContent;
use App\Ai\Agents\ContentWriter;
use App\Data\FieldGenerationData;
use App\Enums\GeneratableFields;
use App\Models\User;
use Laravel\Ai\Prompts\AgentPrompt;

function makeGenerationData(GeneratableFields $field = GeneratableFields::BLOG_ARTICLE_SUMMARY, ?string $notes = 'Mention Laravel'): FieldGenerationData {
    return new FieldGenerationData(
        field: $field,
        prompt: 'Write the summary.',
        notes: $notes,
        locale: 'it',
        current_value: 'Old summary',
        context: ['Title' => 'My article'],
    );
}

it('returns the generated text and sends the whole brief to the agent', function (): void {
    ContentWriter::fake([['text' => '  Generated summary  ']])->preventStrayPrompts();
    $this->actingAs(User::factory()->admin()->create());

    expect(GenerateFieldContent::run(makeGenerationData()))->toBe('Generated summary');

    ContentWriter::assertPrompted(fn (AgentPrompt $prompt): bool => $prompt->contains('Write the summary.')
        && $prompt->contains('Italian')
        && $prompt->contains('Mention Laravel')
        && $prompt->contains('Title: My article')
        && $prompt->contains('Old summary'));
});

it('asks for html only on rich text fields', function (): void {
    ContentWriter::fake([['text' => '<p>Body</p>']])->preventStrayPrompts();
    $this->actingAs(User::factory()->admin()->create());

    GenerateFieldContent::run(makeGenerationData(GeneratableFields::BLOG_ARTICLE_CONTENT));

    ContentWriter::assertPrompted(fn (AgentPrompt $prompt): bool => $prompt->contains('Output format: HTML.'));
});

it('clamps the text to the field maximum length', function (): void {
    ContentWriter::fake([['text' => str_repeat('a', 400)]])->preventStrayPrompts();
    $this->actingAs(User::factory()->admin()->create());

    expect(GenerateFieldContent::run(makeGenerationData(GeneratableFields::SEO_TITLE)))->toHaveLength(255);
});

it('omits the notes when blank', function (): void {
    ContentWriter::fake([['text' => 'Text']])->preventStrayPrompts();
    $this->actingAs(User::factory()->admin()->create());

    GenerateFieldContent::run(makeGenerationData(notes: null));

    ContentWriter::assertPrompted(fn (AgentPrompt $prompt): bool => ! $prompt->contains('Notes and raw material'));
});
