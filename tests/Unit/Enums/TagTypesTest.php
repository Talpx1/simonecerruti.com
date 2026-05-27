<?php

declare(strict_types=1);

use App\Enums\TagTypes;

it('exposes the expected backing values', function () {
    expect(TagTypes::TAG->value)->toBe('tag')
        ->and(TagTypes::BLOG_CATEGORY->value)->toBe('blog_category')
        ->and(TagTypes::TECHNOLOGY->value)->toBe('technology');
});

it('has exactly three cases', function () {
    expect(TagTypes::cases())->toHaveCount(3);
});
