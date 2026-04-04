<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use App\Enums\TagTypes;
use App\Models\BlogArticle;
use App\Models\Project;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Spatie\Tags\Tag;

class TagArchive extends Component {
    public Tag $tag;

    /** @var Collection<int, BlogArticle> */
    public Collection $articles;

    /** @var Collection<int, Project> */
    public Collection $projects;

    public int $articles_total = 0;

    public int $projects_total = 0;

    public function mount(string $slug): void {
        $this->tag = Tag::query()
            ->where('slug->'.app()->currentLocale(), $slug)
            ->firstOrFail();

        $this->articles_total = BlogArticle::query()
            ->whereHasCurrentLocaleTranslation()
            ->wherePublished()
            ->withAnyTags([$this->tag->name], TagTypes::TAG->value)
            ->count();

        $this->projects_total = Project::query()
            ->whereHasCurrentLocaleTranslation()
            ->wherePublished()
            ->withAnyTags([$this->tag->name], TagTypes::TAG->value)
            ->count();

        $this->articles = BlogArticle::query()
            ->whereHasCurrentLocaleTranslation()
            ->wherePublished()
            ->withAnyTags([$this->tag->name], TagTypes::TAG->value)
            ->with(['tags', 'media'])
            ->latest('published_at')
            ->get();

        $this->projects = Project::query()
            ->whereHasCurrentLocaleTranslation()
            ->wherePublished()
            ->withAnyTags([$this->tag->name], TagTypes::TAG->value)
            ->with(['tags', 'media'])
            ->latest()
            ->get();
    }

    public function render(): View {
        return view('pages.tag-archive.tag-archive')
            ->title('#'.$this->tag->name);
    }
}
