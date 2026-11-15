<?php

declare(strict_types=1);

use App\Livewire\Components\ContactForm;
use App\Mail\ContactLeadMail;
use App\Mail\ContactLeadToUserMail;
use App\Models\ContactLead;
use App\Models\Lead;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

use function Pest\Livewire\livewire;

beforeEach(fn () => $this->withoutVite());

/**
 * @return array<string, mixed>
 */
function validContactFormData(): array {
    return [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'company_name' => 'Acme',
        'email' => 'john@example.com',
        'phone' => '+39 333 1234567',
        'message' => 'I would like to discuss a project with you.',
        'acceptance' => true,
    ];
}

it('creates a contact lead with an associated lead and sends both emails', function () {
    Mail::fake();

    livewire(ContactForm::class)
        ->fillForm(validContactFormData())
        ->call('onSubmit', ['recaptcha_token' => 'token'])
        ->assertHasNoFormErrors()
        ->assertNotified();

    $this->assertDatabaseHas(ContactLead::class, [
        'email' => 'john@example.com',
        'acceptance' => true,
    ]);
    expect(Lead::count())->toBe(1);

    Mail::assertQueued(ContactLeadMail::class);
    Mail::assertQueued(ContactLeadToUserMail::class);
});

it('requires the mandatory fields and an accepted data-processing consent', function () {
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

    expect(ContactLead::count())->toBe(0);
});

it('rejects a submission without data-processing consent', function () {
    livewire(ContactForm::class)
        ->fillForm([...validContactFormData(), 'acceptance' => false])
        ->call('onSubmit', ['recaptcha_token' => 'token'])
        ->assertHasFormErrors(['acceptance']);

    expect(ContactLead::count())->toBe(0);
});

it('rejects a submission that fails the recaptcha check in production', function () {
    // Fill the form in the testing environment, then switch to production so
    // verifyRecaptcha() runs against the faked Google endpoint.
    $component = livewire(ContactForm::class)->fillForm(validContactFormData());

    app()->detectEnvironment(fn () => 'production');
    Http::fake([
        config()->string('services.recaptcha.verify_url') => Http::response([
            'success' => true,
            'action' => 'contact_form',
            'score' => 0.1,
        ]),
    ]);

    $component
        ->call('onSubmit', ['recaptcha_token' => 'token'])
        ->assertStatus(400);

    expect(ContactLead::count())->toBe(0);
});

it('accepts a submission that passes the recaptcha check in production', function () {
    Mail::fake();

    $component = livewire(ContactForm::class)->fillForm(validContactFormData());

    app()->detectEnvironment(fn () => 'production');
    Http::fake([
        config()->string('services.recaptcha.verify_url') => Http::response([
            'success' => true,
            'action' => 'contact_form',
            'score' => 0.9,
        ]),
    ]);

    $component
        ->call('onSubmit', ['recaptcha_token' => 'token'])
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(ContactLead::class, ['email' => 'john@example.com']);
});
