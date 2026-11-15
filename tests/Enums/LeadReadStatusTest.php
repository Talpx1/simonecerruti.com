<?php

declare(strict_types=1);

use App\Enums\LeadReadStatus;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;

describe('getColor', function (): void {
    it('maps the status to its color', function (LeadReadStatus $status): void {
        $expected = [
            LeadReadStatus::UNREAD->value => Color::Blue,
            LeadReadStatus::READ->value => Color::Green,
        ];

        expect($status->getColor())->toBe($expected[$status->value]);
    })->with(LeadReadStatus::cases());
});

describe('getIcon', function (): void {
    it('maps the status to an envelope icon', function (LeadReadStatus $status): void {
        $expected = [
            LeadReadStatus::UNREAD->value => Heroicon::OutlinedEnvelope,
            LeadReadStatus::READ->value => Heroicon::OutlinedEnvelopeOpen,
        ];

        expect($status->getIcon())->toBe($expected[$status->value]);
    })->with(LeadReadStatus::cases());
});

describe('getLabel', function (): void {
    it('translates the label in the requested locale', function (LeadReadStatus $status, string $locale): void {
        $expected = [
            LeadReadStatus::UNREAD->value => 'Non letto',
            LeadReadStatus::READ->value => 'Letto',
        ];

        expect($status->getLabel($locale))->toBe($expected[$status->value]);
    })->with(LeadReadStatus::cases())->with(['en', 'it']);
});
