<?php

declare(strict_types=1);

use App\Ai\Agents\ContentWriter;
use App\Enums\Abilities;
use App\Enums\GeneratableFields;
use App\Filament\Resources\BlogArticles\Pages\EditBlogArticle;
use App\Filament\Resources\Campaigns\Pages\EditCampaign;
use App\Filament\Resources\Clients\Pages\EditClient;
use App\Filament\Resources\Projects\Pages\EditProject;
use App\Models\BlogArticle;
use App\Models\Campaign;
use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use App\Settings\AiSettings;
use Filament\Actions\Testing\TestAction;
use Laravel\Ai\Exceptions\InsufficientCreditsException;
use Laravel\Ai\Exceptions\ProviderOverloadedException;
use Laravel\Ai\Prompts\AgentPrompt;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    authenticateAsAdmin();
});

describe('plain text fields', function (): void {
    it('fills the field with the generated text', function (): void {
        ContentWriter::fake([['text' => 'A generated summary']])->preventStrayPrompts();
        $article = BlogArticle::factory()->create();

        livewire(EditBlogArticle::class, ['record' => $article->id])
            ->callAction(TestAction::make('generateWithAi')->schemaComponent('summary'), ['prompt' => 'Summarize it.', 'notes' => 'Keep it short'])
            ->assertNotified(__('Text generated'))
            ->assertSchemaStateSet(['summary' => 'A generated summary']);

        ContentWriter::assertPrompted(fn (AgentPrompt $prompt): bool => $prompt->contains('Summarize it.') && $prompt->contains('Keep it short'));
    });

    it('sends the sibling fields as context', function (): void {
        ContentWriter::fake([['text' => 'A title']])->preventStrayPrompts();
        $article = BlogArticle::factory()->create(['summary' => ['it' => 'Il riassunto', 'en' => 'The summary']]);

        livewire(EditBlogArticle::class, ['record' => $article->id])
            ->callAction(TestAction::make('generateWithAi')->schemaComponent('title'), ['prompt' => 'Title it.']);

        ContentWriter::assertPrompted(fn (AgentPrompt $prompt): bool => $prompt->contains('Summary: Il riassunto'));
    });

    it('prefills the modal with the default prompt', function (): void {
        $article = BlogArticle::factory()->create();

        livewire(EditBlogArticle::class, ['record' => $article->id])
            ->mountAction(TestAction::make('generateWithAi')->schemaComponent('summary'))
            ->assertSchemaStateSet(['prompt' => GeneratableFields::BLOG_ARTICLE_SUMMARY->getDefaultPrompt()]);
    });
});

describe('rich text fields', function (): void {
    it('fills the editor with the generated html', function (): void {
        ContentWriter::fake([['text' => '<h2>Heading</h2><p>Generated body</p>']])->preventStrayPrompts();
        $article = BlogArticle::factory()->create();

        livewire(EditBlogArticle::class, ['record' => $article->id])
            ->callAction(TestAction::make('generateWithAi')->schemaComponent('content'), ['prompt' => 'Rewrite it.'])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($article->refresh()->getTranslation('content', 'it'))->toContain('Generated body');
    });
});

describe('other resources', function (): void {
    it('generates a project field', function (): void {
        ContentWriter::fake([['text' => 'A generated goal']])->preventStrayPrompts();
        $project = Project::factory()->create();

        livewire(EditProject::class, ['record' => $project->id])
            ->callAction(TestAction::make('generateWithAi')->schemaComponent('goal'), ['prompt' => 'Write the goal.'])
            ->assertSchemaStateSet(['goal' => 'A generated goal']);
    });

    it('generates a client field', function (): void {
        ContentWriter::fake([['text' => 'A generated client description']])->preventStrayPrompts();
        $client = Client::factory()->create();

        livewire(EditClient::class, ['record' => $client->id])
            ->callAction(TestAction::make('generateWithAi')->schemaComponent('description'), ['prompt' => 'Describe.'])
            ->assertSchemaStateSet(['description' => 'A generated client description']);
    });

    it('generates a campaign field', function (): void {
        ContentWriter::fake([['text' => 'Clean notes']])->preventStrayPrompts();
        $campaign = Campaign::factory()->create();

        livewire(EditCampaign::class, ['record' => $campaign->id])
            ->callAction(TestAction::make('generateWithAi')->schemaComponent('notes'), ['prompt' => 'Clean up.'])
            ->assertSchemaStateSet(['notes' => 'Clean notes']);
    });

    it('generates a seo field', function (): void {
        ContentWriter::fake([['text' => 'A meta title']])->preventStrayPrompts();
        $article = BlogArticle::factory()->create();

        livewire(EditBlogArticle::class, ['record' => $article->id])
            ->callAction(TestAction::make('generateWithAi')->schemaComponent('seo.title'), ['prompt' => 'Meta title.'])
            ->assertSchemaStateSet(['seo.title' => 'A meta title']);
    });
});

describe('save as default', function (): void {
    it('stores the edited prompt when requested', function (): void {
        ContentWriter::fake([['text' => 'Text']])->preventStrayPrompts();
        $article = BlogArticle::factory()->create();

        livewire(EditBlogArticle::class, ['record' => $article->id])
            ->callAction(TestAction::make('generateWithAi')->schemaComponent('summary'), ['prompt' => 'My prompt', 'save_as_default' => true]);

        expect(resolve(AiSettings::class)->prompts)->toBe([GeneratableFields::BLOG_ARTICLE_SUMMARY->value => 'My prompt']);
    });

    it('does not store the prompt by default', function (): void {
        ContentWriter::fake([['text' => 'Text']])->preventStrayPrompts();
        $article = BlogArticle::factory()->create();

        livewire(EditBlogArticle::class, ['record' => $article->id])
            ->callAction(TestAction::make('generateWithAi')->schemaComponent('summary'), ['prompt' => 'My prompt']);

        expect(resolve(AiSettings::class)->prompts)->toBeEmpty();
    });
});

describe('failures', function (): void {
    it('tells the user when the credits are exhausted and keeps the field untouched', function (): void {
        ContentWriter::fake([fn () => throw new InsufficientCreditsException('no credits')])->preventStrayPrompts();
        $article = BlogArticle::factory()->create(['summary' => ['it' => 'Old', 'en' => 'Old']]);

        livewire(EditBlogArticle::class, ['record' => $article->id])
            ->callAction(TestAction::make('generateWithAi')->schemaComponent('summary'), ['prompt' => 'Summarize.'])
            ->assertNotified(__('The AI credits are exhausted'))
            ->assertSchemaStateSet(['summary' => 'Old']);
    });

    it('tells the user when the provider fails', function (): void {
        ContentWriter::fake([fn () => throw new ProviderOverloadedException('overloaded')])->preventStrayPrompts();
        $article = BlogArticle::factory()->create();

        livewire(EditBlogArticle::class, ['record' => $article->id])
            ->callAction(TestAction::make('generateWithAi')->schemaComponent('summary'), ['prompt' => 'Summarize.'])
            ->assertNotified(__('The AI could not generate the text'));
    });

    it('requires a prompt', function (): void {
        $article = BlogArticle::factory()->create();

        livewire(EditBlogArticle::class, ['record' => $article->id])
            ->callAction(TestAction::make('generateWithAi')->schemaComponent('summary'), ['prompt' => ''])
            ->assertHasActionErrors(['prompt' => 'required']);
    });
});

describe('authorization', function (): void {
    it('allows admins to generate and manage the AI settings', function (Abilities $ability): void {
        expect(Gate::allows($ability->value))->toBeTrue();
    })->with(Abilities::cases());

    it('denies users who cannot access the panel', function (Abilities $ability): void {
        $this->actingAs(User::factory()->create());

        expect(Gate::allows($ability->value))->toBeFalse();
    })->with(Abilities::cases());

    it('denies guests', function (Abilities $ability): void {
        auth()->logout();

        expect(Gate::allows($ability->value))->toBeFalse();
    })->with(Abilities::cases());
});
