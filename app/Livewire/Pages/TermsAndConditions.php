<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use Illuminate\View\View;
use Livewire\Component;

class TermsAndConditions extends Component {
    public function render(): View {
        return view('pages.legal.terms-and-conditions')
            ->title(__('Terms and Conditions'));
    }
}
