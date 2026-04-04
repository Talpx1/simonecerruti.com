<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use Illuminate\View\View;
use Livewire\Component;

class HowIWork extends Component {
    public function render(): View {
        return view('pages.how-i-work.how-i-work')
            ->title(__('How I work'));
    }
}
