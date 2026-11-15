<?php

declare(strict_types=1);

use App\Enums\LeadReadStatus;
use App\Models\ContactLead;
use App\Models\Lead;

describe('attributes', function (): void {
    describe('read_status', function (): void {
        it('is unread when read_at is null', function (): void {
            $lead = Lead::factory()->create();

            expect($lead->read_status)->toBe(LeadReadStatus::UNREAD);
        });

        it('is read when read_at is set', function (): void {
            $lead = Lead::factory()->create();

            expect($lead->read_status)->toBe(LeadReadStatus::UNREAD)
                ->and($lead->update(['read_at' => now()]))->toBeTrue()
                ->and($lead->fresh()->read_status)->toBe(LeadReadStatus::READ);
        });
    });

    describe('source', function (): void {
        it('resolves the source name from the leadable type', function (): void {
            $lead = Lead::factory()->create();

            expect($lead->source)->toBe(__('Contact Form'));
        });
    });
});

describe('relationships', function (): void {
    describe('leadable', function (): void {
        it('morphs to the underlying lead model', function (): void {
            $lead = Lead::factory()->create();

            expect($lead->leadable)->toBeInstanceOf(ContactLead::class);
        });
    });
});

describe('factory', function (): void {
    it('can be pinned', function (): void {
        expect(Lead::factory()->create()->is_pinned)->toBeFalse()
            ->and(Lead::factory()->pinned()->create()->is_pinned)->toBeTrue();
    });

    it('can be read', function (): void {
        expect(Lead::factory()->create()->read_at)->toBeNull()
            ->and(Lead::factory()->read()->create()->read_at)->not->toBeNull();
    });
});
