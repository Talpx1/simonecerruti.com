<?php

declare(strict_types=1);

namespace App\Livewire\Pages\Project;

use App\Livewire\Concerns\HasLoadMore;
use App\Models\Project;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\View\View;
use Livewire\Component;

class ProjectList extends Component {
    use HasLoadMore;

    private function projectsBaseQuery(): Builder {
        return Project::query()
            ->where('published', true)
            ->with(['tags', 'media'])
            ->orderByDesc('featured')
            ->orderByDesc('created_at');
    }

    public function mount(): void {
        $this->useLoadMore([
            'projects' => ['per_page' => 6, 'query_method' => 'projectsBaseQuery'],
        ]);

        $this->fetchLoadMoreData('projects');
    }

    public function render(): View {
        return view('pages.projects.list')
            ->layout('components.layouts.public.index', [
                'title' => __('Projects'),
            ]);
    }
}
