<?php

declare(strict_types=1);

namespace App\Livewire\Pages\Project;

use App\Actions\GetAdjacentProjects;
use App\Models\Project;
use Illuminate\View\View;
use Livewire\Component;

class ProjectShow extends Component {
    public Project $project;

    public function mount(Project $project): void {
        $this->project = $project->load(['tags', 'media', 'seo', 'client']);
    }

    public function render(): View {
        return view('pages.projects.show', [
            'adjacent_projects' => GetAdjacentProjects::run($this->project),
        ])
            ->layout('layouts.public.index', [
                'seo_data' => $this->project->toSeoData(),
            ]);
    }
}
