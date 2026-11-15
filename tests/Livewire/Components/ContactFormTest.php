<?php

declare(strict_types=1);

use App\Livewire\Components\ContactForm;
use App\Mail\ContactLeadMail;
use App\Mail\ContactLeadToUserMail;
use App\Models\ContactLead;
use App\Models\Lead;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;

describe('submission', function (): void {
    it('creates a contact lead with an associated lead and sends both emails', function (): void {
        expect(ContactLead::query()->count())->toBe(0)
            ->and(Lead::query()->count())->toBe(0);

        livewire(ContactForm::class)
            ->fillForm(getValidContactFormData())
            ->call('onSubmit', ['recaptcha_token' => 'token'])
            ->assertHasNoFormErrors()
            ->assertNotified();

        assertDatabaseHas(ContactLead::class, [
            'email' => 'john@example.com',
            'acceptance' => true,
        ]);

        expect(ContactLead::query()->count())->toBe(1)
            ->and(Lead::query()->count())->toBe(1);

        Mail::assertQueued(ContactLeadMail::class);
        Mail::assertQueued(ContactLeadToUserMail::class);
    });

    it('rejects a submission missing the mandatory fields', function (): void {
        livewire(ContactForm::class)
            ->fillForm([
                'first_name' => null,
                'last_name' => null,
                'email' => null,
                'message' => null,
                'acceptance' => false,
            ])
            ->call('onSubmit', ['recaptcha_token' => 'token'])
            ->assertHasFormErrors(['first_name', 'last_name', 'email', 'message', 'acceptance']);

        expect(ContactLead::query()->count())->toBe(0);
    });

    it('rejects a submission without data-processing consent', function (): void {
        livewire(ContactForm::class)
            ->fillForm([...getValidContactFormData(), 'acceptance' => false])
            ->call('onSubmit', ['recaptcha_token' => 'token'])
            ->assertHasFormErrors(['acceptance']);

        expect(ContactLead::query()->count())->toBe(0);
        Mail::assertNothingQueued();
    });
});

describe('recaptcha in production', function (): void {
    afterEach(function (): void {
        app()->detectEnvironment(fn (): string => 'testing');
    });

    it('rejects a submission that fails the check', function (): void {
        $component = livewire(ContactForm::class)->fillForm(getValidContactFormData());

        fakeRecaptchaResponseInProduction(0.1);

        $component
            ->call('onSubmit', ['recaptcha_token' => 'token'])
            ->assertStatus(400);

        Http::assertSentCount(1);
        expect(ContactLead::query()->count())->toBe(0);
        Mail::assertNothingQueued();
    });

    it('accepts a submission that passes the check', function (): void {
        $component = livewire(ContactForm::class)->fillForm(getValidContactFormData());

        fakeRecaptchaResponseInProduction(0.9);

        expect(ContactLead::query()->count())->toBe(0);

        $component
            ->call('onSubmit', ['recaptcha_token' => 'token'])
            ->assertHasNoFormErrors();

        assertDatabaseHas(ContactLead::class, ['email' => 'john@example.com']);
    });
});
