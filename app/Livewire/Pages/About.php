<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use Illuminate\View\View;
use Livewire\Component;

class About extends Component {
    public function render(): View {
        return view('pages.about.about')
            ->title(__('About'));
    }
}
