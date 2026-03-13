<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use App\Models\BlogArticle;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class Blog extends Component {
    use WithoutUrlPagination, WithPagination;

    private int $per_page = 12;

    public function loadMore(): void { // FIXME: make this work properly and possibly without refetching already fetched articles
        $this->per_page += 12;
    }

    public function render(): View {
        $featured_articles = BlogArticle::query()
            ->whereHasCurrentLocaleTranslation()
            ->wherePublished()
            ->orderBy('featured')
            ->orderByDesc('published_at')
            ->limit(6)
            ->get();

        $articles = BlogArticle::query()
            ->whereHasCurrentLocaleTranslation()
            ->wherePublished()
            ->whereNotIn('id', $featured_articles->pluck('id'))
            ->orderByDesc('published_at')
            ->paginate($this->per_page);

        return view('pages.blog.blog')
            ->layout('components.layouts.public.index', [
                'title' => __('Blog'),
            ])
            ->with([
                'featured_articles' => $featured_articles,
                'articles' => $articles,
            ]);
    }
}
