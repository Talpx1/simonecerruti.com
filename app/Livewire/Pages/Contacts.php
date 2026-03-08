<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use Illuminate\View\View;
use Livewire\Component;

class Contacts extends Component {
    public function render(): View {
        return view('pages.contacts.contacts')
            ->layout('components.layouts.public.index', [
                'title' => __('Contacts'),
            ]);
    }
}
