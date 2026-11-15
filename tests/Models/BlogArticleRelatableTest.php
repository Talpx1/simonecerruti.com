<?php

declare(strict_types=1);

use App\Models\BlogArticle;
use App\Models\BlogArticleRelatable;
use App\Models\Project;

describe('attributes', function (): void {
    describe('show_route', function (): void {
        it('points to the project page for a project', function (): void {
            $relatable = BlogArticleRelatable::factory()->forProject(Project::factory()->create())->create();

            expect($relatable->show_route)->toBe(route('project.show', $relatable->relatable->slug));
        });

        it('points to the blog article page for a blog article', function (): void {
            $relatable = BlogArticleRelatable::factory()->forBlogArticle(BlogArticle::factory()->create())->create();

            expect($relatable->show_route)->toBe(route('blog_article.show', $relatable->relatable->slug));
        });
    });

    describe('title', function (): void {
        it('is the project title for a project', function (): void {
            $project = Project::factory()->create();
            $relatable = BlogArticleRelatable::factory()->forProject($project)->create();

            expect($relatable->title)->toBe($project->title);
        });

        it('is the article title for a blog article', function (): void {
            $article = BlogArticle::factory()->create();
            $relatable = BlogArticleRelatable::factory()->forBlogArticle($article)->create();

            expect($relatable->title)->toBe($article->title);
        });
    });

    describe('post_type', function (): void {
        it('is the project label for a project', function (): void {
            $relatable = BlogArticleRelatable::factory()->forProject()->create();

            expect($relatable->post_type)->toBe(__('Project'));
        });

        it('is the blog article label for a blog article', function (): void {
            $relatable = BlogArticleRelatable::factory()->forBlogArticle()->create();

            expect($relatable->post_type)->toBe(__('Blog article'));
        });
    });
});
