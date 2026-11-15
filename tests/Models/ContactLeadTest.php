<?php

declare(strict_types=1);

use App\Models\ContactLead;
use App\Models\Lead;

describe('full_name attribute', function () {
    it('joins first and last name', function () {
        $lead = ContactLead::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'company_name' => null,
        ]);

        expect($lead->full_name)->toBe('John Doe');
    });

    it('appends the company name when present', function () {
        $lead = ContactLead::factory()->create([
            'first_name' => 'Jane',
            'last_name' => 'Roe',
            'company_name' => 'Acme',
        ]);

        expect($lead->full_name)->toBe('Jane Roe (Acme)');
    });
});

describe('lead_title attribute', function () {
    it('builds a localized title from the full name', function () {
        $lead = ContactLead::factory()->create([
            'first_name' => 'Jane',
            'last_name' => 'Roe',
            'company_name' => null,
        ]);

        expect($lead->lead_title)->toBe(__('Contact request from :full_name', ['full_name' => 'Jane Roe']));
    });
});

describe('lead source', function () {
    it('exposes the contact form source name', function () {
        expect(ContactLead::getLeadSourceName())->toBe(__('Contact Form'));
    });
});

describe('infolist components', function () {
    it('exposes the expected infolist entries', function () {
        expect(ContactLead::factory()->create()->getInfolistComponents())->toHaveCount(8);
    });
});

describe('lead relationship', function () {
    it('has one polymorphic lead', function () {
        $contactLead = ContactLead::factory()->create();
        $contactLead->lead()->create(['read_at' => null, 'is_pinned' => false]);

        expect($contactLead->refresh()->lead)->toBeInstanceOf(Lead::class);
    });
});
