<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\View\View;
use Livewire\Component;

class Home extends Component implements HasActions, HasForms {
    use InteractsWithActions;
    use InteractsWithForms;

    public function render(): View {
        return view('pages.home.home')
            ->layout('components.layouts.public.index', [
                'title' => config()->string('app.name'),
                'suffix' => false,
            ]);
    }

    public function openContactFormAction(?string $source = null) {
        Action::make('openContactForm')
            ->schema([
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
            ])
            ->action(function (array $data) {});
    }
}
