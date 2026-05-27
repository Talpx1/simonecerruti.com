<?php

declare(strict_types=1);

use App\Enums\BlogCategories;
use App\Enums\TagTypes;
use Illuminate\Support\Facades\DB;
use Spatie\Tags\Tag;

it('exposes the configured tag model', function () {
    expect(BlogCategories::getModelClass())->toBe(config()->string('tags.tag_model'));
});

describe('sync', function () {
    it('syncs every category into the tags table', function () {
        BlogCategories::sync();

        expect(DB::table('tags')->where('type', TagTypes::BLOG_CATEGORY->value)->count())->toBe(2);
    });

    it('is idempotent', function () {
        BlogCategories::sync();
        BlogCategories::sync();

        expect(DB::table('tags')->where('type', TagTypes::BLOG_CATEGORY->value)->count())->toBe(2);
    });

    it('does not delete tags created outside of the enum (partial sync)', function () {
        BlogCategories::sync();
        $custom = Tag::findOrCreate('A custom category', TagTypes::BLOG_CATEGORY->value);

        BlogCategories::sync();

        expect(Tag::find($custom->id))->not->toBeNull();
    });
});

describe('model', function () {
    it('resolves the related tag model by value', function () {
        BlogCategories::sync();

        expect(BlogCategories::PRACTICAL->model())->toBeInstanceOf(Tag::class)
            ->and(BlogCategories::PRACTICAL->model()->id)->toBe(BlogCategories::PRACTICAL->value);
    });
});
