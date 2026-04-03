<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use App\Enums\TagTypes;
use App\Models\BlogArticle;
use App\Models\Project;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Component;
use Spatie\Tags\Tag;

class Home extends Component {
    /**
     * @return Collection<int, Project>
     */
    private function getProjects(): Collection {
        return Project::query()
            ->whereHasCurrentLocaleTranslation()
            ->where('published', true)
            ->orderByDesc('featured')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
    }

    /**
     * @return Collection<int, Tag>
     */
    private function getProjectTags(): Collection {
        return Tag::query()
            ->orderByDesc(
                DB::table('taggables')
                    ->selectRaw('count(*)')
                    ->whereColumn('taggables.tag_id', 'tags.id')
            )
            ->withType('tag')
            ->limit(10)
            ->get();
    }

    /**
     * @return Collection<int, BlogArticle>
     */
    private function getBlogArticles(string $category): Collection {
        $category_tag = Tag::query()
            ->where('type', 'blog_category')
            ->where('slug->en', $category)
            ->first();

        if (! $category_tag) {
            return new Collection([]);
        }

        return BlogArticle::query()
            ->whereHasCurrentLocaleTranslation()
            ->withAllTags($category_tag, TagTypes::BLOG_CATEGORY->value)
            ->wherePublished()
            ->orderByDesc('featured')
            ->orderByDesc('published_at')
            ->limit(2)
            ->get();
    }

    public function render(): View {
        return view('pages.home.home')
            ->layout('layouts.public.index', [
                'title' => config()->string('app.name'),
                'suffix' => false,
            ])
            ->with([
                'projects' => $this->getProjects(),
                'project_tags' => $this->getProjectTags(),
                'practical_blog_articles' => $this->getBlogArticles('practical'),
                'technical_blog_articles' => $this->getBlogArticles('technical'),
            ]);
    }
}
