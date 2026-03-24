<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

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
            ->withType('tags')
            ->limit(10)
            ->get();
    }

    public function render(): View {
        return view('pages.home.home')
            ->layout('components.layouts.public.index', [
                'title' => config()->string('app.name'),
                'suffix' => false,
            ])
            ->with([
                'projects' => $this->getProjects(),
                'project_tags' => $this->getProjectTags(),
            ]);
    }
}
