<?php

declare(strict_types=1);

namespace App\Ai\Agents;

use App\Settings\AiSettings;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Ai\Attributes\MaxTokens;
use Laravel\Ai\Attributes\Model;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Attributes\Timeout;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Promptable;

#[Provider(Lab::Anthropic)]
#[Model('claude-opus-5-5')]
#[Timeout(180)]
#[MaxTokens(16000)]
final class ContentWriter implements Agent, HasStructuredOutput {
    use Promptable;

    public function __construct(private readonly AiSettings $settings) {}

    public function instructions(): string {
        return $this->settings->style_guide."\n\n".implode(' ', [
            'Reply only with the text of the requested field, ready to be saved as is.',
            'No preamble, no explanations, no surrounding quotation marks.',
            'When HTML is requested, use only these tags: p, h2, h3, strong, em, u, a, ul, ol, li, blockquote, br. Never use h1 and never use markdown.',
        ]);
    }

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array {
        return [
            'text' => $schema->string()->required(),
        ];
    }
}
