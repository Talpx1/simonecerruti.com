<?php

declare(strict_types=1);

namespace App\Livewire\Pages\BlogArticle;

use App\Enums\TagTypes;
use App\Livewire\Concerns\HasLoadMore;
use App\Models\BlogArticle;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Spatie\Tags\Tag;

class BlogArticleList extends Component {
    use HasLoadMore;

    #[Url(as: 'category')]
    public ?string $active_category = null;

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

    public function filterByCategory(?string $slug): void {
        $this->active_category = $slug;

        $this->resetLoadMore('articles');
    }

    private function resolveActiveTag(): ?Tag {
        if (! $this->active_category) {
            return null;
        }

        return Tag::query()
            ->where('type', TagTypes::BLOG_CATEGORY->value)
            ->where(function ($query): void {
                foreach (array_keys(App::supportedLocales()) as $locale) {
                    $query->orWhere("slug->{$locale}", $this->active_category);
                }
            })
            ->first();
    }

    private function articlesBaseQuery(): Builder {
        $tag = $this->resolveActiveTag();

        return BlogArticle::query()
            ->wherePublished()
            ->with(['tags', 'media'])
            ->when(
                $tag !== null,
                fn (Builder $query) => $query->withAnyTags($tag, TagTypes::BLOG_CATEGORY->value)
            )
            ->when(
                $tag === null && $this->featured,
                fn (Builder $query) => $query->where('id', '!=', $this->featured->id)
            )
            ->latest('published_at');
    }

    public function mount(): void {
        $this->active_category = $this->resolveActiveTag()?->slug;

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
