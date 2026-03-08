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

class ContactLeadMail extends Mailable implements ShouldQueue {
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
            replyTo: [$this->lead->email],
            subject: __('Contact request from :full_name', ['full_name' => $this->lead->full_name]),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content {
        return new Content(
            text: 'mail.leads.contact.text',
            markdown: 'mail.leads.contact.markdown',
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
