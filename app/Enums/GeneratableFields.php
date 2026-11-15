<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasLocalizedLabel;
use Filament\Support\Contracts\HasLabel;

enum GeneratableFields: string implements HasLabel {
    use HasLocalizedLabel;

    case BLOG_ARTICLE_TITLE = 'blog_article_title';
    case BLOG_ARTICLE_SUMMARY = 'blog_article_summary';
    case BLOG_ARTICLE_CONTENT = 'blog_article_content';
    case PROJECT_TITLE = 'project_title';
    case PROJECT_SHORT_DESCRIPTION = 'project_short_description';
    case PROJECT_DESCRIPTION = 'project_description';
    case PROJECT_STARTING_POINT = 'project_starting_point';
    case PROJECT_GOAL = 'project_goal';
    case PROJECT_OUTCOME = 'project_outcome';
    case PROJECT_FEATURE_TITLE = 'project_feature_title';
    case PROJECT_FEATURE_DESCRIPTION = 'project_feature_description';
    case PROJECT_HOME_SUMMARY = 'project_home_summary';
    case CLIENT_DESCRIPTION = 'client_description';
    case TAG_DESCRIPTION = 'tag_description';
    case CAMPAIGN_DESCRIPTION = 'campaign_description';
    case CAMPAIGN_NOTES = 'campaign_notes';
    case SEO_TITLE = 'seo_title';
    case SEO_DESCRIPTION = 'seo_description';
    case SEO_OG_TITLE = 'seo_og_title';
    case SEO_OG_DESCRIPTION = 'seo_og_description';
    case SEO_TWITTER_TITLE = 'seo_twitter_title';
    case SEO_TWITTER_DESCRIPTION = 'seo_twitter_description';

    public function getDefaultPrompt(): string {
        return match ($this) {
            self::BLOG_ARTICLE_TITLE => 'Write a clear, specific and search-friendly title for this blog article. No clickbait, no quotation marks, no trailing period.',
            self::BLOG_ARTICLE_SUMMARY => 'Write a 2-3 sentence summary of this blog article for the blog index and link previews. Say what the reader will learn and why it matters.',
            self::BLOG_ARTICLE_CONTENT => 'Rewrite and format the provided material (the current content and/or the notes) as a complete blog article. Keep every fact, figure and claim of the source and never invent new ones. Use a short introduction, h2/h3 sections, short paragraphs, lists where they help scanning, and a closing paragraph with the main takeaway. Do not repeat the article title as a heading.',
            self::PROJECT_TITLE => 'Write a concise project title: what was built, for whom or with which key value. No quotation marks, no trailing period.',
            self::PROJECT_SHORT_DESCRIPTION => 'Write a one-paragraph teaser of this project: what was built, for whom and the main result.',
            self::PROJECT_DESCRIPTION => 'Write the full case study of this project: context, what was built, the technical choices explained to a non-technical reader, and the results. Use only facts found in the provided material.',
            self::PROJECT_STARTING_POINT => 'Describe the situation before the project started: the problem, the constraints and the tools the client was using. 2-3 sentences.',
            self::PROJECT_GOAL => 'State the goal of the project in 2-3 sentences: what the client wanted to achieve and how success was measured.',
            self::PROJECT_OUTCOME => 'State the outcome of the project in one short line, using concrete figures from the material when available.',
            self::PROJECT_FEATURE_TITLE => 'Write a short name for this project feature, 2-5 words.',
            self::PROJECT_FEATURE_DESCRIPTION => 'Describe this project feature in 1-2 sentences, focusing on the benefit for the user.',
            self::PROJECT_HOME_SUMMARY => 'Write a brief summary of this project for the home page: one short paragraph, much shorter than the full description, highlighting the key result.',
            self::CLIENT_DESCRIPTION => 'Write a neutral 2-3 sentence description of this client: what they do and in which sector.',
            self::TAG_DESCRIPTION => 'Write a one-sentence description of what this tag covers, for readers and search engines.',
            self::CAMPAIGN_DESCRIPTION => 'Describe the purpose and the target audience of this campaign in 1-3 sentences.',
            self::CAMPAIGN_NOTES => 'Organize the provided raw notes into clean, concise internal notes. Do not add information that is not in the notes.',
            self::SEO_TITLE => 'Write the meta title of this page: at most 60 characters, the main keyword near the start, no clickbait.',
            self::SEO_DESCRIPTION => 'Write the meta description of this page: 140-155 characters, summarizing the page and inviting the click. Plain text.',
            self::SEO_OG_TITLE => 'Write the Open Graph title used when this page is shared on social networks: at most 70 characters, engaging but accurate.',
            self::SEO_OG_DESCRIPTION => 'Write the Open Graph description used when this page is shared on social networks: 1-2 sentences, accurate and inviting.',
            self::SEO_TWITTER_TITLE => 'Write the title used when this page is shared on Twitter/X: at most 70 characters, engaging but accurate.',
            self::SEO_TWITTER_DESCRIPTION => 'Write the description used when this page is shared on Twitter/X: 1-2 sentences, accurate and inviting.',
        };
    }

    public function getSubject(): string {
        return match ($this) {
            self::BLOG_ARTICLE_TITLE, self::BLOG_ARTICLE_SUMMARY, self::BLOG_ARTICLE_CONTENT => 'blog article',
            self::PROJECT_TITLE,
            self::PROJECT_SHORT_DESCRIPTION,
            self::PROJECT_DESCRIPTION,
            self::PROJECT_STARTING_POINT,
            self::PROJECT_GOAL,
            self::PROJECT_OUTCOME,
            self::PROJECT_FEATURE_TITLE,
            self::PROJECT_FEATURE_DESCRIPTION,
            self::PROJECT_HOME_SUMMARY => 'portfolio project',
            self::CLIENT_DESCRIPTION => 'client',
            self::TAG_DESCRIPTION => 'tag',
            self::CAMPAIGN_DESCRIPTION, self::CAMPAIGN_NOTES => 'marketing campaign',
            self::SEO_TITLE,
            self::SEO_DESCRIPTION,
            self::SEO_OG_TITLE,
            self::SEO_OG_DESCRIPTION,
            self::SEO_TWITTER_TITLE,
            self::SEO_TWITTER_DESCRIPTION => 'web page (SEO metadata)',
        };
    }

    public function getGroupLabel(): string {
        return match ($this) {
            self::BLOG_ARTICLE_TITLE, self::BLOG_ARTICLE_SUMMARY, self::BLOG_ARTICLE_CONTENT => __('Blog articles'),
            self::PROJECT_TITLE,
            self::PROJECT_SHORT_DESCRIPTION,
            self::PROJECT_DESCRIPTION,
            self::PROJECT_STARTING_POINT,
            self::PROJECT_GOAL,
            self::PROJECT_OUTCOME,
            self::PROJECT_FEATURE_TITLE,
            self::PROJECT_FEATURE_DESCRIPTION,
            self::PROJECT_HOME_SUMMARY => __('Projects'),
            self::CLIENT_DESCRIPTION => __('Clients'),
            self::TAG_DESCRIPTION => __('Tags'),
            self::CAMPAIGN_DESCRIPTION, self::CAMPAIGN_NOTES => __('Campaigns'),
            self::SEO_TITLE,
            self::SEO_DESCRIPTION,
            self::SEO_OG_TITLE,
            self::SEO_OG_DESCRIPTION,
            self::SEO_TWITTER_TITLE,
            self::SEO_TWITTER_DESCRIPTION => __('SEO'),
        };
    }

    public function isRichText(): bool {
        return match ($this) {
            self::BLOG_ARTICLE_CONTENT, self::PROJECT_DESCRIPTION, self::PROJECT_HOME_SUMMARY => true,
            default => false,
        };
    }

    public function getMaxCharacters(): ?int {
        return match ($this) {
            self::PROJECT_SHORT_DESCRIPTION => 500,
            self::BLOG_ARTICLE_TITLE,
            self::PROJECT_TITLE,
            self::PROJECT_OUTCOME,
            self::PROJECT_FEATURE_TITLE,
            self::SEO_TITLE,
            self::SEO_DESCRIPTION,
            self::SEO_OG_TITLE,
            self::SEO_TWITTER_TITLE => 255,
            default => null,
        };
    }

    /**
     * Sibling fields sent to the AI as context: the label shown to the AI mapped to the state path relative to the field.
     *
     * @return array<string, string>
     */
    public function getContextFields(): array {
        return match ($this) {
            self::BLOG_ARTICLE_TITLE => ['Summary' => 'summary', 'Content' => 'content'],
            self::BLOG_ARTICLE_SUMMARY => ['Title' => 'title', 'Content' => 'content'],
            self::BLOG_ARTICLE_CONTENT => ['Title' => 'title', 'Summary' => 'summary'],
            self::PROJECT_TITLE => ['Short description' => 'short_description', 'Description' => 'description'],
            self::PROJECT_SHORT_DESCRIPTION => ['Title' => 'title', 'Description' => 'description', 'Goal' => 'goal', 'Outcome' => 'outcome'],
            self::PROJECT_DESCRIPTION => ['Title' => 'title', 'Short description' => 'short_description', 'Starting point' => 'starting_point', 'Goal' => 'goal', 'Outcome' => 'outcome'],
            self::PROJECT_STARTING_POINT, self::PROJECT_GOAL, self::PROJECT_OUTCOME => ['Title' => 'title', 'Short description' => 'short_description', 'Description' => 'description'],
            self::PROJECT_FEATURE_TITLE => ['Feature description' => 'description', 'Project title' => '../../title'],
            self::PROJECT_FEATURE_DESCRIPTION => ['Feature title' => 'title', 'Project title' => '../../title'],
            self::PROJECT_HOME_SUMMARY => ['Title' => 'title', 'Short description' => 'short_description', 'Description' => 'description'],
            self::CLIENT_DESCRIPTION => ['Name' => 'name'],
            self::TAG_DESCRIPTION => ['Name' => 'name'],
            self::CAMPAIGN_DESCRIPTION, self::CAMPAIGN_NOTES => ['Name' => 'name', 'Source' => 'source'],
            self::SEO_TITLE,
            self::SEO_DESCRIPTION,
            self::SEO_OG_TITLE,
            self::SEO_OG_DESCRIPTION,
            self::SEO_TWITTER_TITLE,
            self::SEO_TWITTER_DESCRIPTION => ['Page title' => '../title', 'Page summary' => '../summary', 'Page description' => '../short_description'],
        };
    }
}
