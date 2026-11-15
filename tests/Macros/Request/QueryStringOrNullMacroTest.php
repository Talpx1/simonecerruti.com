<?php

declare(strict_types=1);

use Illuminate\Http\Request;

describe('queryStringOrNull', function (): void {
    it('returns the query string value when it is a non-empty string', function (): void {
        expect(Request::create('/?q=abc')->queryStringOrNull('q'))->toBe('abc');
    });

    it('returns null when the value is not a usable string', function (string $uri): void {
        expect(Request::create($uri)->queryStringOrNull('q'))->toBeNull();
    })->with([
        'empty' => '/?q=',
        'missing' => '/',
    ]);
});
