<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use App\Enums\RobotsDirective;
use Illuminate\View\View;
use Livewire\Component;

class TermsAndConditions extends Component {
    public function render(): View {
        $view = view('pages.legal.terms-and-conditions');

        $view->title(__('Terms and Conditions'));
        $view->layout('layouts::public.index', ['robots' => RobotsDirective::NOINDEX->value]);

        return $view;
    }
}
