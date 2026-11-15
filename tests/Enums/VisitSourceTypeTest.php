<?php

declare(strict_types=1);

use App\Enums\VisitSourceType;
use Filament\Support\Colors\Color;

describe('values', function () {
    it('exposes the expected backing values', function () {
        expect(VisitSourceType::DIRECT->value)->toBe('direct')
            ->and(VisitSourceType::INTERNAL->value)->toBe('internal')
            ->and(VisitSourceType::UNKNOWN->value)->toBe('unknown');
    });

    it('only contains the three fallback buckets', function () {
        expect(VisitSourceType::cases())->toHaveCount(3);
    });
});

describe('getColor', function () {
    it('returns a color array for every case', function () {
        foreach (VisitSourceType::cases() as $case) {
            expect($case->getColor())->toBeArray();
        }
    });

    it('uses slate for direct', function () {
        expect(VisitSourceType::DIRECT->getColor())->toBe(Color::Slate);
    });
});
