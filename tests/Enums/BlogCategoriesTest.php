<?php

declare(strict_types=1);

use App\Enums\BlogCategories;
use App\Enums\TagTypes;
use App\Models\Tag;

describe('getModelClass', function (): void {
    it('exposes the configured tag model', function (): void {
        expect(BlogCategories::getModelClass())->toBe(config()->string('tags.tag_model'));
    });
});

describe('sync', function (): void {
    it('syncs every category into the tags table as a blog category', function (): void {
        $count = fn (): int => Tag::query()->where('type', TagTypes::BLOG_CATEGORY)->count();

        expect($count())->toBe(0)
            ->and(BlogCategories::sync())->toBeNull()
            ->and($count())->toBe(\count(BlogCategories::cases()));
    });

    it('keeps the tags created outside of the enum', function (): void {
        BlogCategories::sync();
        $custom_tag = Tag::findOrCreate('A custom category', TagTypes::BLOG_CATEGORY->value);

        expect(Tag::query()->find($custom_tag->id))->not->toBeNull()
            ->and(BlogCategories::sync())->toBeNull()
            ->and(Tag::query()->find($custom_tag->id))->not->toBeNull();
    });
});

describe('model', function (): void {
    it('resolves the related tag by value', function (BlogCategories $category): void {
        BlogCategories::sync();

        expect($category->model())->toBeInstanceOf(Tag::class)
            ->and($category->model()->id)->toBe($category->value);
    })->with(BlogCategories::cases());
});
