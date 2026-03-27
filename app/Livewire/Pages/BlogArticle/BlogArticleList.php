<?php

declare(strict_types=1);

namespace App\Livewire\Pages\BlogArticle;

use App\Enums\TagTypes;
use App\Livewire\Concerns\HasLoadMore;
use App\Models\BlogArticle;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Spatie\Tags\Tag;

class BlogArticleList extends Component {
    use HasLoadMore;

    public ?int $active_category = null;

    /**
     * @return Collection<int, Tag>
     */
    #[Computed]
    public function categories(): Collection {
        return Tag::query()
            ->where('type', TagTypes::BLOG_CATEGORY->value)
            ->whereNotNull('name->'.app()->getLocale())
            ->orderBy('name->'.app()->getLocale())
            ->get();
    }

    #[Computed]
    public function featured(): ?BlogArticle {
        if ($this->active_category !== null) {
            return null;
        }

        return BlogArticle::query()
            ->wherePublished()
            ->where('featured', true)
            ->with(['tags', 'media'])
            ->latest('published_at')
            ->first();
    }

    public function filterByCategory(?int $category_id): void {
        $this->active_category = $category_id;
        $this->resetLoadMore('articles');
    }

    private function articlesBaseQuery(): Builder {
        return BlogArticle::query()
            ->wherePublished()
            ->with(['tags', 'media'])
            ->when(
                $this->active_category !== null,
                fn (Builder $query) => $query->withAnyTags(
                    Tag::find($this->active_category),
                    TagTypes::BLOG_CATEGORY->value
                )
            )
            ->when(
                $this->active_category === null && $this->featured,
                fn (Builder $query) => $query->where('id', '!=', $this->featured->id)
            )
            ->latest('published_at');
    }

    public function mount(): void {
        $this->useLoadMore([
            'articles' => ['per_page' => 6, 'query_method' => 'articlesBaseQuery'],
        ]);

        $this->fetchLoadMoreData('articles');
    }

    public function render(): View {
        return view('pages.blog-article.list')
            ->layout('components.layouts.public.index', [
                'title' => __('Blog'),
            ]);
    }
}
