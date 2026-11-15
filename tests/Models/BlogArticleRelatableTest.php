<?php

declare(strict_types=1);

use App\Models\BlogArticle;
use App\Models\BlogArticleRelatable;
use App\Models\Project;

describe('related project', function () {
    it('exposes the show route, title and post type for a project', function () {
        $project = Project::factory()->create();
        $relatable = BlogArticleRelatable::factory()->forProject($project)->create();

        expect($relatable->show_route)->toBe(route('project.show', $relatable->relatable->slug))
            ->and($relatable->title)->toBe($project->title)
            ->and($relatable->post_type)->toBe(__('Project'));
    });
});

describe('related blog article', function () {
    it('exposes the show route, title and post type for a blog article', function () {
        $article = BlogArticle::factory()->create();
        $relatable = BlogArticleRelatable::factory()->forBlogArticle($article)->create();

        expect($relatable->show_route)->toBe(route('blog_article.show', $relatable->relatable->slug))
            ->and($relatable->title)->toBe($article->title)
            ->and($relatable->post_type)->toBe(__('Blog article'));
    });
});
