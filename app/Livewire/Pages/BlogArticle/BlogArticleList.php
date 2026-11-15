<?php

declare(strict_types=1);

namespace App\Livewire\Pages\BlogArticle;

use App\Actions\GetFeaturedBlogArticles;
use App\Enums\TagTypes;
use App\Models\BlogArticle;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class BlogArticleList extends Component {
    use WithPagination;

    public const int ARTICLES_PER_PAGE = 6;

    #[Url(as: 'category')]
    public ?string $active_category = null;

    /**
     * @return Collection<int, Tag>
     */
    #[Computed]
    public function categories(): Collection {
        return Tag::query()
            ->where('type', TagTypes::BLOG_CATEGORY->value)
            ->whereNotNull('name->'.App::getLocale())
            ->orderBy('name->'.App::getLocale())
            ->get();
    }

    /**
     * @return Collection<int, BlogArticle>
     */
    #[Computed]
    public function featured(): Collection {
        return GetFeaturedBlogArticles::run();
    }

    /**
     * @return LengthAwarePaginator<int, BlogArticle>
     */
    #[Computed]
    public function articles(): LengthAwarePaginator {
        return $this->articlesBaseQuery()->paginate(self::ARTICLES_PER_PAGE);
    }

    public function filterByCategory(?string $slug): void {
        $this->active_category = $slug;

        $this->resetPage();
    }

    private function resolveActiveTag(): ?Tag {
        if (! $this->active_category) {
            return null;
        }

        return Tag::query()
            ->where('type', TagTypes::BLOG_CATEGORY->value)
            ->where(function (Builder $query): void {
                foreach (array_keys(App::supportedLocales()) as $locale) {
                    $query->orWhereJsonContains("slug->{$locale}", $this->active_category);
                }
            })
            ->first();
    }

    /**
     * @return Builder<BlogArticle>
     */
    private function articlesBaseQuery(): Builder {
        $tag = $this->resolveActiveTag();

        $query = BlogArticle::query()
            ->wherePublished()
            ->whereNotIn('id', $this->featured()->modelKeys())
            ->with(['tags', 'media', 'author']);

        if ($tag instanceof Tag) {
            $query->withAnyTags([$tag], TagTypes::BLOG_CATEGORY->value);
        }

        return $query->latest('published_at');
    }

    public function mount(): void {
        $this->active_category = $this->resolveActiveTag()?->slug;
    }

    public function render(): View {
        return view('pages.blog-article.list')
            ->title(__('Blog'));
    }
}
