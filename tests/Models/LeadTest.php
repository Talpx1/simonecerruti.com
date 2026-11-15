<?php

declare(strict_types=1);

use App\Enums\LeadReadStatus;
use App\Models\ContactLead;
use App\Models\Lead;

describe('read_status attribute', function () {
    it('is unread when read_at is null', function () {
        $lead = Lead::factory()->create();

        expect($lead->read_status)->toBe(LeadReadStatus::UNREAD);
    });

    it('is read when read_at is set', function () {
        $lead = Lead::factory()->read()->create();

        expect($lead->read_status)->toBe(LeadReadStatus::READ);
    });
});

describe('source attribute', function () {
    it('resolves the source name from the leadable type', function () {
        $lead = Lead::factory()->create();

        expect($lead->source)->toBe(__('Contact Form'));
    });
});

describe('leadable relationship', function () {
    it('morphs to the underlying lead model', function () {
        $lead = Lead::factory()->create();

        expect($lead->leadable)->toBeInstanceOf(ContactLead::class);
    });
});

describe('factory states', function () {
    it('can be pinned', function () {
        expect(Lead::factory()->pinned()->create()->is_pinned)->toBeTrue();
    });
});
