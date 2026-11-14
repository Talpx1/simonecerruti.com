<?php

declare(strict_types=1);

namespace App\Livewire\Pages\BlogArticle;

use App\Actions\BuildTableOfContents;
use App\Filament\RichContentBlocks\GalleryBlock;
use App\Models\BlogArticle;
use App\Settings\BlogSettings;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Illuminate\View\View;
use Livewire\Component;

class BlogArticleShow extends Component {
    public BlogArticle $blog_article;

    public ?BlogArticle $previous = null;

    public ?BlogArticle $next = null;

    public function mount(BlogArticle $blog_article): void {
        $this->blog_article = $blog_article->load(['tags', 'media', 'relatables.relatable', 'author', 'seo']);
        $this->previous = $blog_article->previous();
        $this->next = $blog_article->next();
    }

    public function render(BlogSettings $blog_settings): View {
        $content = BuildTableOfContents::run(
            RichContentRenderer::make($this->blog_article->content)->customBlocks([GalleryBlock::class])->toHtml()
        );

        $view = view('pages.blog-article.show')
            ->with([
                'related_blog_articles' => $this->blog_article->relatedBlogArticles(),
                'content_html' => $content['html'],
                'table_of_contents' => $content['items'],
                'author_bio' => $blog_settings->getAuthorBioForCurrentLocale(),
            ]);

        $view->layout('layouts.public.index', [
            'seo_data' => $this->blog_article->toSeoData(),
        ]);

        return $view;
    }
}
