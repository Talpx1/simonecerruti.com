<?php

declare(strict_types=1);

use App\Mail\ContactLeadToUserMail;
use App\Models\ContactLead;
use Illuminate\Contracts\Queue\ShouldQueue;

it('uses the company friendly name in the subject', function () {
    $lead = ContactLead::factory()->create();

    $mail = new ContactLeadToUserMail($lead);

    $mail->assertHasSubject(__('You contacted :company_name!', [
        'company_name' => config()->string('company.friendly_name'),
    ]));
});

it('is queued', function () {
    $lead = ContactLead::factory()->create();

    expect(new ContactLeadToUserMail($lead))->toBeInstanceOf(ShouldQueue::class);
});
