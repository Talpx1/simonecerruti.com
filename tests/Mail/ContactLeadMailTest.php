<?php

declare(strict_types=1);

use App\Mail\ContactLeadMail;
use App\Models\ContactLead;
use Illuminate\Contracts\Queue\ShouldQueue;

it('addresses the internal notification with the right subject and reply-to', function () {
    $lead = ContactLead::factory()->create([
        'first_name' => 'Jane',
        'last_name' => 'Roe',
        'company_name' => null,
        'email' => 'jane@example.com',
    ]);

    $mail = new ContactLeadMail($lead);

    $mail->assertHasSubject(__('Contact request from :full_name', ['full_name' => 'Jane Roe']));
    $mail->assertHasReplyTo('jane@example.com');
});

it('is queued', function () {
    $lead = ContactLead::factory()->create();

    expect(new ContactLeadMail($lead))->toBeInstanceOf(ShouldQueue::class);
});
