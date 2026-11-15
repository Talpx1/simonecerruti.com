<?php

declare(strict_types=1);

use App\Enums\TagTypes;

it('exposes the expected backing values', function () {
    expect(TagTypes::TAG->value)->toBe('tag')
        ->and(TagTypes::BLOG_CATEGORY->value)->toBe('blog_category')
        ->and(TagTypes::TECHNOLOGY->value)->toBe('technology')
        ->and(TagTypes::PROJECT_KIND->value)->toBe('project_kind')
        ->and(TagTypes::CAMPAIGN_TAG->value)->toBe('campaign_tag');
});

it('has exactly five cases', function () {
    expect(TagTypes::cases())->toHaveCount(5);
});
