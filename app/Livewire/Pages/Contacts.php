<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\View\View;
use Livewire\Component;

class Contacts extends Component implements HasForms {
    use InteractsWithForms;

    public function render(): View {
        return view('pages.contacts.contacts')
            ->layout('components.layouts.public.index', [
                'title' => config()->string('app.name'),
                'suffix' => false,
            ]);
    }

    public function makeContactForm() {
        return [
            TextInput::make('first_name')
                ->required()
                ->maxLength(255),

            TextInput::make('last_name')
                ->required()
                ->maxLength(255),

            TextInput::make('company_name')
                ->maxLength(255),

            TextInput::make('email')
                ->email()
                ->inputMode('email')
                ->required(),

            TextInput::make('phone')
                ->tel()
                ->inputMode('tel'),

            Textarea::make('message')
                ->required(),
        ];
    }
}
