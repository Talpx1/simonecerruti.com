<?php

declare(strict_types=1);

use App\Mail\ContactLeadMail;
use App\Models\ContactLead;
use Illuminate\Contracts\Queue\ShouldQueue;

describe('envelope', function (): void {
    it('sets the full name in the subject and the lead email as reply-to', function (): void {
        $lead = ContactLead::factory()->create([
            'first_name' => 'Jane',
            'last_name' => 'Roe',
            'company_name' => null,
            'email' => 'jane@example.com',
        ]);

        $mail = new ContactLeadMail($lead);

        $mail->assertHasSubject(__('Contact request from :full_name', ['full_name' => 'Jane Roe']));
        $mail->assertHasReplyTo('jane@example.com');
        expect($mail->hasReplyTo('someone-else@example.com'))->toBeFalse();
    });
});

describe('content', function (): void {
    it('renders the lead details', function (): void {
        $lead = ContactLead::factory()->create(['message' => 'I need a bespoke CRM', 'email' => 'jane@example.com']);

        $mail = new ContactLeadMail($lead);

        $mail->assertSeeInHtml('I need a bespoke CRM');
        $mail->assertSeeInHtml('jane@example.com');
        $mail->assertDontSeeInHtml('a message nobody wrote');
        $mail->assertSeeInText('I need a bespoke CRM');
    });
});

describe('delivery', function (): void {
    it('is queued', function (): void {
        $lead = ContactLead::factory()->create();

        expect(new ContactLeadMail($lead))->toBeInstanceOf(ShouldQueue::class);
    });
});
