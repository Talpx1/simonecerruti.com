<?php

declare(strict_types=1);

use App\Actions\BuildTableOfContents;

describe('items', function () {
    it('lists h2 and h3 headings in document order with their level', function () {
        $result = BuildTableOfContents::run('<h1>Skip</h1><h2>First</h2><p>x</p><h3>Nested</h3><h2>Second</h2><h4>Skip too</h4>');

        expect($result['items'])->toBe([
            ['id' => 'first', 'title' => 'First', 'level' => 2],
            ['id' => 'nested', 'title' => 'Nested', 'level' => 3],
            ['id' => 'second', 'title' => 'Second', 'level' => 2],
        ]);
    });

    it('returns no items when there are no headings', function () {
        $result = BuildTableOfContents::run('<p>Just text</p>');

        expect($result['items'])->toBe([])
            ->and($result['html'])->toBe('<p>Just text</p>');
    });
});

describe('ids', function () {
    it('injects the ids into the returned html', function () {
        $result = BuildTableOfContents::run('<h2>Hello World</h2>');

        expect($result['html'])->toBe('<h2 id="hello-world">Hello World</h2>');
    });

    it('keeps an existing id', function () {
        $result = BuildTableOfContents::run('<h2 id="custom">Title</h2>');

        expect($result['items'][0]['id'])->toBe('custom')
            ->and($result['html'])->toContain('id="custom"');
    });

    it('de-duplicates repeated titles', function () {
        $result = BuildTableOfContents::run('<h2>Same</h2><h3>Same</h3><h2>Same</h2>');

        expect(\array_column($result['items'], 'id'))->toBe(['same', 'same-2', 'same-3']);
    });

    it('does not collide with a pre-existing id declared later in the document', function () {
        $result = BuildTableOfContents::run('<h2>Intro</h2><h3 id="intro">Other</h3>');

        expect(\array_column($result['items'], 'id'))->toBe(['intro-2', 'intro']);
    });

    it('falls back to a generic id when the title has no sluggable characters', function () {
        $result = BuildTableOfContents::run('<h2>???</h2>');

        expect($result['items'][0]['id'])->toBe('section');
    });
});

describe('encoding', function () {
    it('preserves accented characters and entities', function () {
        $result = BuildTableOfContents::run('<h2>Perché è così</h2><p>Città &amp; più — “ok”</p>');

        expect($result['html'])->toContain('Perché è così')
            ->and($result['html'])->toContain('Città &amp; più — “ok”')
            ->and($result['items'][0]['title'])->toBe('Perché è così')
            ->and($result['items'][0]['id'])->toBe('perche-e-cosi');
    });
});
