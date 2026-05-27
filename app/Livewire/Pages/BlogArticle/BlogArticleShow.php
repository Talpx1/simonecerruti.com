<?php

declare(strict_types=1);

namespace App\Livewire\Pages\BlogArticle;

use App\Models\BlogArticle;
use Illuminate\View\View;
use Livewire\Component;

class BlogArticleShow extends Component {
    public BlogArticle $blog_article;

    public ?BlogArticle $previous = null;

    public ?BlogArticle $next = null;

    public function mount(BlogArticle $blog_article): void {
        $this->blog_article = $blog_article->load(['tags', 'media', 'relatables.relatable']);
        $this->previous = $blog_article->previous();
        $this->next = $blog_article->next();
    }

    public function render(): View {
        $view = view('pages.blog-article.show')
            ->with(['related_blog_articles' => $this->blog_article->relatedBlogArticles()]);

        $view->title($this->blog_article->title);

        return $view;
    }
}
