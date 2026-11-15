<?php

declare(strict_types=1);

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;

use function Pest\Laravel\freezeTime;

describe('replacement sources', function (): void {
    it('replaces placeholders from an array', function (): void {
        expect(Str::replacePlaceholders('Hello {name}', ['name' => 'John']))->toBe('Hello John');
    });

    it('replaces placeholders from an object property', function (): void {
        expect(Str::replacePlaceholders('Hello {name}', (object) ['name' => 'Jane']))->toBe('Hello Jane');
    });

    it('replaces placeholders from an object method', function (): void {
        $object = new class {
            public function greeting(): string {
                return 'Hi there';
            }
        };

        expect(Str::replacePlaceholders('{greeting}', $object))->toBe('Hi there');
    });

    it('replaces placeholders from an Arrayable', function (): void {
        $arrayable = new class implements Arrayable {
            /** @return array<string, string> */
            public function toArray(): array {
                return ['city' => 'Turin'];
            }
        };

        expect(Str::replacePlaceholders('{city}', $arrayable))->toBe('Turin');
    });

    it('stringifies non-string replacements', function (): void {
        expect(Str::replacePlaceholders('Count: {count}', ['count' => 5]))->toBe('Count: 5');
    });
});

describe('special placeholders', function (): void {
    it('resolves the timestamp special placeholder', function (): void {
        freezeTime();

        expect(Str::replacePlaceholders('{timestamp}'))->toBe((string) now()->timestamp);
    });

    it('resolves the random special placeholder to a non-empty value', function (): void {
        expect(Str::replacePlaceholders('{random}'))->not->toBe('{random}');
    });

    it('resolves a global function placeholder', function (): void {
        expect(Str::replacePlaceholders('{phpversion}'))->toBe(\phpversion());
    });
});

describe('unmatched placeholders', function (): void {
    it('leaves unmatched placeholders intact', function (): void {
        expect(Str::replacePlaceholders('{unknown}', []))->toBe('{unknown}');
    });

    it('strips unmatched placeholders when asked to', function (): void {
        expect(Str::replacePlaceholders('{name}-{unknown}', ['name' => 'John'], strip_unmatched: true))
            ->toBe('John-');
    });
});
