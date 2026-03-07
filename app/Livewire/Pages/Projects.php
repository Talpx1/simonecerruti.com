<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use Illuminate\View\View;
use Livewire\Component;

class Projects extends Component {
    public function render(): View {
        return view('pages.projects.projects')
            ->layout('components.layouts.public.index', [
                'title' => __('Projects'),
            ]);
    }
}
