<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\ContactLead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactLeadToUserMail extends Mailable implements ShouldQueue {
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(private ContactLead $lead) {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope {
        return new Envelope(
            subject: __('You contacted :company_name!', ['company_name' => config()->string('company.friendly_name')]),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content {
        return new Content(
            text: 'mail.leads.contact.user.text',
            markdown: 'mail.leads.contact.user.markdown',
            with: ['lead' => $this->lead]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array {
        return [];
    }
}
