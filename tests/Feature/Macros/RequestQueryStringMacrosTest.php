<?php

declare(strict_types=1);

use Illuminate\Http\Request;

it('returns the query string value or the default', function (string $uri, ?string $expected) {
    $request = Request::create($uri);

    expect($request->queryStringOrDefault('q', 'fallback'))->toBe($expected ?? 'fallback');
})->with([
    'present' => ['/?q=abc', 'abc'],
    'empty' => ['/?q=', null],
    'missing' => ['/', null],
    'array' => ['/?q[]=1', null],
]);

it('returns the query string value or null', function (string $uri, ?string $expected) {
    expect(Request::create($uri)->queryStringOrNull('q'))->toBe($expected);
})->with([
    'present' => ['/?q=abc', 'abc'],
    'empty' => ['/?q=', null],
    'missing' => ['/', null],
]);
