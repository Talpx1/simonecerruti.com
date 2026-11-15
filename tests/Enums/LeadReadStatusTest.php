<?php

declare(strict_types=1);

use App\Enums\LeadReadStatus;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;

describe('getColor', function () {
    it('maps each status to a color', function () {
        expect(LeadReadStatus::UNREAD->getColor())->toBe(Color::Blue)
            ->and(LeadReadStatus::READ->getColor())->toBe(Color::Green);
    });
});

describe('getIcon', function () {
    it('maps each status to an envelope icon', function () {
        expect(LeadReadStatus::UNREAD->getIcon())->toBe(Heroicon::OutlinedEnvelope)
            ->and(LeadReadStatus::READ->getIcon())->toBe(Heroicon::OutlinedEnvelopeOpen);
    });
});

describe('values', function () {
    it('exposes the expected backing values', function () {
        expect(LeadReadStatus::UNREAD->value)->toBe('unread')
            ->and(LeadReadStatus::READ->value)->toBe('read');
    });
});
