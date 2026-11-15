<?php

declare(strict_types=1);

use App\Enums\TagTypes;
use App\Models\Tag;
use Database\Seeders\BlogCategorySeeder;

it('creates the two blog categories with translated names', function (): void {
    $this->seed(BlogCategorySeeder::class);

    $categories = Tag::query()->where('type', TagTypes::BLOG_CATEGORY->value)->get();

    expect($categories)->toHaveCount(2)
        ->and($categories->map->getTranslation('name', 'en')->sort()->values()->all())->toBe(['practical', 'technical'])
        ->and($categories->map->getTranslation('name', 'it')->sort()->values()->all())->toBe(['pratico', 'tecnico'])
        ->and($categories->map->getTranslation('slug', 'it')->sort()->values()->all())->toBe(['pratico', 'tecnico']);
});

it('is idempotent across repeated runs', function (): void {
    $this->seed(BlogCategorySeeder::class);
    $this->seed(BlogCategorySeeder::class);

    expect(Tag::query()->where('type', TagTypes::BLOG_CATEGORY->value)->count())->toBe(2);
});

it('leaves categories created from the panel untouched', function (): void {
    $custom = Tag::findOrCreate('A custom category', TagTypes::BLOG_CATEGORY->value);

    $this->seed(BlogCategorySeeder::class);

    expect(Tag::query()->whereKey($custom->getKey())->exists())->toBeTrue()
        ->and(Tag::query()->where('type', TagTypes::BLOG_CATEGORY->value)->count())->toBe(3);
});
