<?php

declare(strict_types=1);

namespace App\Livewire\Pages\Project;

use App\Models\Project;
use Illuminate\View\View;
use Livewire\Component;

class ProjectShow extends Component {
    public Project $project;

    public function mount(Project $project): void {
        $this->project = $project->load(['tags', 'media']);
    }

    public function render(): View {
        return view('pages.projects.show')
            ->title($this->project->title);
    }
}
