<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use App\DataTransferObjects\WorkCardData;
use App\Enums\ServiceAreas;
use App\Models\Project;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Component;

class Home extends Component {
    private const int WORKS_LIMIT = 3;

    /**
     * @return Collection<int, Project>
     */
    private function getProjects(): Collection {
        return Project::query()
            ->featuredRanked()
            ->with(['client', 'tags', 'media'])
            ->limit(self::WORKS_LIMIT)
            ->get();
    }

    /**
     * @param  Collection<int, Project>  $projects
     * @return list<WorkCardData>
     */
    private function getWorks(Collection $projects): array {
        return \array_values($projects
            ->map(fn (Project $project): WorkCardData => new WorkCardData(
                title: $project->title,
                kind: $project->kind_names,
                image_url: $project->hasMedia('featured_image') ? $project->featured_image_url : null,
                summary_html: \filled($project->home_summary) ? RichContentRenderer::make($project->home_summary)->toHtml() : null,
                url: route('project.show', $project->slug),
            ))
            ->all());
    }

    public function render(): View {
        $view = view('pages.home.home')
            ->with([
                'works' => $this->getWorks($this->getProjects()),
                'service_routes' => [
                    'erp' => ServiceAreas::MANAGEMENT_ERP_CRM->getRouteName(),
                    'web' => ServiceAreas::WEB_ECOMMERCE_PLATFORMS->getRouteName(),
                ],
            ]);

        $view->layout('layouts.public.index', [
            'title' => config()->string('app.name'),
            'suffix' => false,
        ]);

        return $view;
    }
}
