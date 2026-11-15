<?php

declare(strict_types=1);

use App\Actions\CalculateReadingTime;

describe('reading time', function (): void {
    it('returns at least one minute for empty or tiny content', function (): void {
        expect(CalculateReadingTime::run(''))->toBe(1)
            ->and(CalculateReadingTime::run('<p>Hello</p>'))->toBe(1);
    });

    it('rounds minutes up', function (): void {
        expect(CalculateReadingTime::run('<p>'.\str_repeat('word ', 221).'</p>'))->toBe(2)
            ->and(CalculateReadingTime::run('<p>'.\str_repeat('word ', 220).'</p>'))->toBe(1);
    });

    it('does not count html tags or attributes as words', function (): void {
        $html = '<div class="a b c d e"><p>one</p><img src="x.jpg" alt="y"></div>';

        expect(CalculateReadingTime::run($html, words_per_minute: 1))->toBe(1);
    });

    it('counts accented words correctly', function (): void {
        expect(CalculateReadingTime::run('<p>perché è così città già</p>', words_per_minute: 1))->toBe(5);
    });

    it('honours a custom reading speed', function (): void {
        expect(CalculateReadingTime::run('<p>'.\str_repeat('word ', 100).'</p>', words_per_minute: 50))->toBe(2);
    });
});
