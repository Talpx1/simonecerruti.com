<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use Illuminate\View\View;
use Livewire\Component;

class CookiePolicy extends Component {
    public function render(): View {
        return view('pages.legal.cookie-policy')
            ->layout('components.layouts.public.index', [
                'title' => __('Cookie Policy'),
            ]);
    }
}
