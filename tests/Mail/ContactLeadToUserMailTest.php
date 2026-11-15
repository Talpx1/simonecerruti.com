<?php

declare(strict_types=1);

use App\Mail\ContactLeadToUserMail;
use App\Models\ContactLead;
use Illuminate\Contracts\Queue\ShouldQueue;

describe('envelope', function (): void {
    it('uses the company friendly name in the subject', function (): void {
        $lead = ContactLead::factory()->create();

        $mail = new ContactLeadToUserMail($lead);

        $mail->assertHasSubject(__('You contacted :company_name!', [
            'company_name' => config()->string('company.friendly_name'),
        ]));
        expect($mail->hasSubject('A different subject'))->toBeFalse();
    });
});

describe('content', function (): void {
    it('renders the summary of the message the lead sent', function (): void {
        $lead = ContactLead::factory()->create(['message' => 'I need a bespoke CRM']);

        $mail = new ContactLeadToUserMail($lead);

        $mail->assertSeeInHtml('I need a bespoke CRM');
        $mail->assertSeeInText('I need a bespoke CRM');
        $mail->assertDontSeeInHtml('a message nobody wrote');
    });
});

describe('delivery', function (): void {
    it('is queued', function (): void {
        $lead = ContactLead::factory()->create();

        expect(new ContactLeadToUserMail($lead))->toBeInstanceOf(ShouldQueue::class);
    });
});
