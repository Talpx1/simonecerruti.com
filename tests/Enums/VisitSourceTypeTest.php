<?php

declare(strict_types=1);

use App\Enums\VisitSourceType;
use Filament\Support\Colors\Color;

describe('getColor', function (): void {
    it('maps the source type to its color', function (VisitSourceType $source_type): void {
        $expected = [
            VisitSourceType::DIRECT->value => Color::Slate,
            VisitSourceType::INTERNAL->value => Color::Gray,
            VisitSourceType::UNKNOWN->value => Color::Zinc,
        ];

        expect($source_type->getColor())->toBe($expected[$source_type->value]);
    })->with(VisitSourceType::cases());
});

describe('getLabel', function (): void {
    it('translates the label in the requested locale', function (VisitSourceType $source_type, string $locale): void {
        $expected = [
            'en' => [
                VisitSourceType::DIRECT->value => 'Direct',
                VisitSourceType::INTERNAL->value => 'Internal',
                VisitSourceType::UNKNOWN->value => 'Unknown',
            ],
            'it' => [
                VisitSourceType::DIRECT->value => 'Diretto',
                VisitSourceType::INTERNAL->value => 'Interno',
                VisitSourceType::UNKNOWN->value => 'Sconosciuto',
            ],
        ];

        expect($source_type->getLabel($locale))->toBe($expected[$locale][$source_type->value]);
    })->with(VisitSourceType::cases())->with(['en', 'it']);
});
