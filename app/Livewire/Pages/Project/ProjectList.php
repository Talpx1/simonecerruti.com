<?php

declare(strict_types=1);

namespace App\Livewire\Pages\Project;

use Illuminate\View\View;
use Livewire\Component;

class ProjectList extends Component {
    public function render(): View {
        return view('pages.projects.list')
            ->layout('components.layouts.public.index', [
                'title' => __('Projects'),
            ]);
    }
}
