<?php

declare(strict_types=1);

namespace App\Livewire\Components;

use App\Filament\Components\FormDataProcessingAcceptance;
use App\Livewire\Concerns\HasRecaptcha;
use App\Mail\ContactLeadMail;
use App\Mail\ContactLeadToUserMail;
use App\Models\ContactLead;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class ContactForm extends Component implements HasSchemas {
    use HasRecaptcha, InteractsWithSchemas;

    /** @var array<string, mixed>|null */
    public ?array $data = [];

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

    /**
     * @param  array<string, mixed>  $form_params
     */
    #[On('formSubmitted.components.contact-form')]
    public function onSubmit(array $form_params): void {
        $this->verifyRecaptcha($form_params['recaptcha_token']);

        $contact_lead = ContactLead::create([
            ...$this->form->getState(),
            'ip' => request()->ip(),
        ]);

        $contact_lead->lead()->create([
            'read_at' => null,
            'is_pinned' => false,
        ]);

        Mail::to(config()->string('mail.internal_recipient'))->locale(config()->string('app.locale'))->send(new ContactLeadMail($contact_lead));
        Mail::to($contact_lead->email)->locale(app()->currentLocale())->send(new ContactLeadToUserMail($contact_lead));

        Notification::make()
            ->title(__('Got it! I\'ll get back to you as soon as possible.'))
            ->duration(50000)
            ->success()
            ->send();

        $this->form->fill();
    }

    public function mount(): void {
        $this->form->fill();
    }

    public function render(): View {
        return view('pages.contacts.components.contact-form');
    }
}
