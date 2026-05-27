<?php

declare(strict_types=1);

use App\Enums\TagTypes;
use Illuminate\Support\Facades\DB;

it('syncs enums that implement SyncsToDatabase', function () {
    $this->artisan('enums:sync')->assertSuccessful();

    expect(DB::table('tags')->where('type', TagTypes::BLOG_CATEGORY->value)->count())->toBe(2);
});

it('is idempotent across repeated runs', function () {
    $this->artisan('enums:sync')->assertSuccessful();
    $this->artisan('enums:sync')->assertSuccessful();

    expect(DB::table('tags')->where('type', TagTypes::BLOG_CATEGORY->value)->count())->toBe(2);
});
