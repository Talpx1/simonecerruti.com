<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use Illuminate\View\View;
use Livewire\Component;

class Home extends Component {
    public function render(): View {
        return view('pages.home.home')
            ->layout('components.layouts.public.index', [
                'title' => config()->string('app.name'),
                'suffix' => false,
            ]);
    }
}
