<?php

declare(strict_types=1);

use App\Enums\TagTypes;
use App\Models\Tag;

use function Pest\Laravel\artisan;

describe('enums:sync', function (): void {
    it('syncs enums that implement SyncsToDatabase', function (): void {
        expect(Tag::query()->where('type', TagTypes::BLOG_CATEGORY->value)->count())->toBe(0);

        artisan('enums:sync')->expectsOutputToContain('Synced')->assertExitCode(0);

        expect(Tag::query()->where('type', TagTypes::BLOG_CATEGORY->value)->count())->toBe(2);
    });

    it('is idempotent across repeated runs', function (): void {
        expect(Tag::query()->where('type', TagTypes::BLOG_CATEGORY->value)->count())->toBe(0);

        artisan('enums:sync')->assertExitCode(0);
        artisan('enums:sync')->assertExitCode(0);

        expect(Tag::query()->where('type', TagTypes::BLOG_CATEGORY->value)->count())->toBe(2);
    });
});
