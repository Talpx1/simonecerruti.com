<?php

declare(strict_types=1);

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;

it('replaces placeholders from an array', function () {
    expect(Str::replacePlaceholders('Hello {name}', ['name' => 'John']))->toBe('Hello John');
});

it('replaces placeholders from an object property', function () {
    expect(Str::replacePlaceholders('Hello {name}', (object) ['name' => 'Jane']))->toBe('Hello Jane');
});

it('replaces placeholders from an object method', function () {
    $object = new class {
        public function greeting(): string {
            return 'Hi there';
        }
    };

    expect(Str::replacePlaceholders('{greeting}', $object))->toBe('Hi there');
});

it('replaces placeholders from an Arrayable', function () {
    $arrayable = new class implements Arrayable {
        /** @return array<string, string> */
        public function toArray(): array {
            return ['city' => 'Turin'];
        }
    };

    expect(Str::replacePlaceholders('{city}', $arrayable))->toBe('Turin');
});

it('resolves the timestamp special placeholder', function () {
    $this->freezeTime();

    expect(Str::replacePlaceholders('{timestamp}'))->toBe((string) now()->timestamp);
});

it('resolves the random special placeholder to a non-empty value', function () {
    expect(Str::replacePlaceholders('{random}'))->not->toBe('{random}');
});

it('resolves a global function placeholder', function () {
    expect(Str::replacePlaceholders('{phpversion}'))->toBe(phpversion());
});

it('stringifies non-string replacements', function () {
    expect(Str::replacePlaceholders('Count: {count}', ['count' => 5]))->toBe('Count: 5');
});

it('leaves unmatched placeholders intact', function () {
    expect(Str::replacePlaceholders('{unknown}', []))->toBe('{unknown}');
});
