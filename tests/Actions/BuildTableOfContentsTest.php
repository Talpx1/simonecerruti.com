<?php

declare(strict_types=1);

use App\Actions\BuildTableOfContents;
use App\DataTransferObjects\TableOfContentsEntryData;

describe('entries', function (): void {
    it('lists h2 and h3 headings in document order with their level', function (): void {
        $entries = BuildTableOfContents::run('<h1 id="skip">Skip</h1><h2 id="first">First</h2><p>x</p><h3 id="nested">Nested</h3><h2 id="second">Second</h2><h4 id="skip-too">Skip too</h4>');

        expect($entries)->toEqual([
            new TableOfContentsEntryData('first', 'First', 2),
            new TableOfContentsEntryData('nested', 'Nested', 3),
            new TableOfContentsEntryData('second', 'Second', 2),
        ]);
    });

    it('ignores headings without an id', function (): void {
        $entries = BuildTableOfContents::run('<h2>No id</h2><h2 id="with-id">With id</h2>');

        expect($entries)->toEqual([new TableOfContentsEntryData('with-id', 'With id', 2)]);
    });

    it('returns no entries when there are no headings', function (): void {
        expect(BuildTableOfContents::run('<p>Just text</p>'))->toBe([]);
    });

    it('preserves accented characters in titles', function (): void {
        $entries = BuildTableOfContents::run('<h2 id="perche">Perché è così</h2>');

        expect($entries)->toEqual([new TableOfContentsEntryData('perche', 'Perché è così', 2)]);
    });
});
