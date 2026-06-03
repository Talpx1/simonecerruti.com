<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use Illuminate\View\View;
use Livewire\Component;

class CookiePolicy extends Component {
    public function render(): View {
        $view = view('pages.legal.cookie-policy');

        $view->title(__('Cookie Policy'));
        $view->layout('layouts::public.index', ['robots' => 'noindex']);

        return $view;
    }
}
