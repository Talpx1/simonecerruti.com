<?php

declare(strict_types=1);

use App\Actions\AddHeadingIds;

describe('id injection', function (): void {
    it('injects ids into h2 and h3 headings', function (): void {
        expect(AddHeadingIds::run('<h2>Hello World</h2><h3>Nested</h3>'))
            ->toBe('<h2 id="hello-world">Hello World</h2><h3 id="nested">Nested</h3>');
    });

    it('keeps an existing id', function (): void {
        expect(AddHeadingIds::run('<h2 id="custom">Title</h2>'))->toBe('<h2 id="custom">Title</h2>');
    });

    it('leaves html without headings untouched', function (): void {
        expect(AddHeadingIds::run('<p>Just text</p>'))->toBe('<p>Just text</p>');
    });
});

describe('id uniqueness', function (): void {
    it('de-duplicates repeated titles', function (): void {
        expect(AddHeadingIds::run('<h2>Same</h2><h3>Same</h3><h2>Same</h2>'))
            ->toBe('<h2 id="same">Same</h2><h3 id="same-2">Same</h3><h2 id="same-3">Same</h2>');
    });

    it('does not collide with a pre-existing id declared later in the document', function (): void {
        expect(AddHeadingIds::run('<h2>Intro</h2><h3 id="intro">Other</h3>'))
            ->toBe('<h2 id="intro-2">Intro</h2><h3 id="intro">Other</h3>');
    });
});

describe('id generation', function (): void {
    it('falls back to a generic id when the title has no sluggable characters', function (): void {
        expect(AddHeadingIds::run('<h2>???</h2>'))->toBe('<h2 id="section">???</h2>');
    });

    it('preserves accented characters and entities', function (): void {
        $html = AddHeadingIds::run('<h2>Perché è così</h2><p>Città &amp; più — “ok”</p>');

        expect($html)->toContain('id="perche-e-cosi"')
            ->and($html)->toContain('Città &amp; più — “ok”');
    });
});
