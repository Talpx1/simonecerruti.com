<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use Illuminate\View\View;
use Livewire\Component;

final class Contacts extends Component {
    public function render(): View {
        $view = view('pages.contacts.contacts');

        $view->layout('layouts.public.index', [
            'description' => __('Let\'s talk about your project: tailor-made websites, e-commerce, platforms and management software. Write to me or book a free, no-obligation call.'),
        ]);

        $view->title(__('Contacts'));

        return $view;
    }
}
