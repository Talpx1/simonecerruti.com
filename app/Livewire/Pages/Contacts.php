<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use App\Filament\Components\FormDataProcessingAcceptance;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\View\View;
use Livewire\Component;

class Contacts extends Component implements HasSchemas {
    use InteractsWithSchemas;

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function render(): View {
        return view('pages.contacts.contacts')
            ->layout('components.layouts.public.index', [
                'title' => __('Contacts'),
            ]);
    }

    public function mount(): void {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema {
        return $schema
            ->components(array_map(
                fn (\Filament\Schemas\Components\Component $component) => $component->extraAttributes([
                    'class' => 'rounded-none',
                ]),
                $this->makeContactForm()
            ))
            ->columns(['xs' => 1, 'lg' => 3, 'xl' => 5])
            ->statePath('data');
    }

    /**
     * @return \Filament\Schemas\Components\Component[]
     */
    public function makeContactForm(): array {
        return [
            TextInput::make('first_name')
                ->label(__('First Name'))
                ->required()
                ->maxLength(255),

            TextInput::make('last_name')
                ->label(__('Last Name'))
                ->required()
                ->maxLength(255),

            TextInput::make('company_name')
                ->label(__('Company Name'))
                ->maxLength(255),

            TextInput::make('email')
                ->label(__('Email'))
                ->email()
                ->inputMode('email')
                ->required(),

            TextInput::make('phone')
                ->label(__('Phone'))
                ->tel()
                ->inputMode('tel'),

            Textarea::make('message')
                ->label(__('Message'))
                ->columnSpanFull()
                ->required(),

            FormDataProcessingAcceptance::make(),
        ];
    }

    public function onSubmit(array $form_params): void {
        //
    }
}
