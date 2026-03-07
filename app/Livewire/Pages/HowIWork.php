<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use Illuminate\View\View;
use Livewire\Component;

class HowIWork extends Component {
    public function render(): View {
        return view('pages.how_i_work.how_i_work')
            ->layout('components.layouts.public.index', [
                'title' => __('How I work'),
            ]);
    }
}
