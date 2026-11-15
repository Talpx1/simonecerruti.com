<?php

declare(strict_types=1);

use Illuminate\Http\Request;

describe('queryStringOrDefault', function (): void {
    it('returns the query string value when it is a non-empty string', function (): void {
        expect(Request::create('/?q=abc')->queryStringOrDefault('q', 'fallback'))->toBe('abc');
    });

    it('returns the default when the value is not a usable string', function (string $uri): void {
        expect(Request::create($uri)->queryStringOrDefault('q', 'fallback'))->toBe('fallback');
    })->with([
        'empty' => '/?q=',
        'missing' => '/',
        'array' => '/?q[]=1',
    ]);
});
