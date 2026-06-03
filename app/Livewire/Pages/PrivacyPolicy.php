<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use Illuminate\View\View;
use Livewire\Component;

class PrivacyPolicy extends Component {
    public function render(): View {
        $view = view('pages.legal.privacy-policy');

        $view->title(__('Privacy Policy'));
        $view->layout('layouts::public.index', ['robots' => 'noindex']);

        return $view;
    }
}
