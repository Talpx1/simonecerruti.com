<?php

declare(strict_types=1);

use App\Models\ContactLead;
use App\Models\Lead;

describe('attributes', function (): void {
    describe('full_name', function (): void {
        it('joins first and last name', function (): void {
            $contact_lead = ContactLead::factory()->withoutCompany()->create([
                'first_name' => 'John',
                'last_name' => 'Doe',
            ]);

            expect($contact_lead->full_name)->toBe('John Doe');
        });

        it('appends the company name when present', function (): void {
            $contact_lead = ContactLead::factory()->create([
                'first_name' => 'Jane',
                'last_name' => 'Roe',
                'company_name' => 'Acme',
            ]);

            expect($contact_lead->full_name)->toBe('Jane Roe (Acme)');
        });
    });

    describe('lead_title', function (): void {
        it('builds a localized title from the full name', function (): void {
            $contact_lead = ContactLead::factory()->withoutCompany()->create([
                'first_name' => 'Jane',
                'last_name' => 'Roe',
            ]);

            expect($contact_lead->lead_title)->toBe(__('Contact request from :full_name', ['full_name' => 'Jane Roe']));
        });
    });
});

describe('accessors', function (): void {
    describe('getLeadSourceName', function (): void {
        it('exposes the contact form source name', function (): void {
            expect(ContactLead::getLeadSourceName())->toBe(__('Contact Form'));
        });
    });
});

describe('relationships', function (): void {
    describe('lead', function (): void {
        it('has one polymorphic lead', function (): void {
            $contact_lead = ContactLead::factory()->create();

            expect($contact_lead->lead)->toBeNull()
                ->and($contact_lead->lead()->create(['read_at' => null, 'is_pinned' => false]))->toBeInstanceOf(Lead::class)
                ->and($contact_lead->refresh()->lead)->toBeInstanceOf(Lead::class);
        });
    });
});

describe('filament', function (): void {
    describe('getInfolistComponents', function (): void {
        it('exposes the expected infolist entries', function (): void {
            expect(ContactLead::factory()->create()->getInfolistComponents())->toHaveCount(8);
        });
    });
});
