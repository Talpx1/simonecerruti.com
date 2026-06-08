<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use App\Models\BlogArticle;
use App\Models\Project;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Component;

final class Services extends Component {
    /**
     * @return Collection<int, Project>
     */
    private function featuredProjects(int $limit): Collection {
        return Project::query()->featuredRanked()->limit($limit)->get();
    }

    private function featuredArticle(): ?BlogArticle {
        return BlogArticle::query()
            ->whereHasCurrentLocaleTranslation()
            ->wherePublished()
            ->orderByDesc('featured')
            ->orderByDesc('published_at')
            ->first();
    }

    public function render(): View {
        $projects = $this->featuredProjects(2);

        return view('pages.services.services')
            ->with([
                'cards_projects' => $projects,
                'cards_article' => $this->featuredArticle(),
                'area_one_project' => $projects->first(),
                // Contextual CTAs intentionally hidden for now (the design referenced
                // specific items not yet curated). Set these to a Project / BlogArticle
                // to surface the Area 02 / Area 03 contextual links — the view already
                // guards them with @if and the translations are in place.
                'area_two_project' => null,
                'area_three_article' => null,
            ])
            ->title(__('Services'));
    }
}
