<?php

declare(strict_types=1);

use App\Enums\Concerns\SeedDb;
use Illuminate\Support\Facades\DB;

/**
 * Fixture that syncs to the tags table with the default (non-partial,
 * non-forced) behaviour so the orphan-protection branch can be exercised.
 */
enum SeedDbStrictFixture: int {
    use SeedDb;

    case ONE = 1;

    public static function table(): string {
        return 'tags';
    }

    /**
     * @return array<string, mixed>
     */
    private function dbMap(): array {
        return [
            'id' => $this->value,
            'name' => json_encode(['en' => 'One']),
            'slug' => json_encode(['en' => 'one']),
            'type' => 'fake',
        ];
    }
}

/**
 * Fixture identical to the above but with forced sync enabled.
 */
enum SeedDbForcedFixture: int {
    use SeedDb;

    case ONE = 1;

    public static function table(): string {
        return 'tags';
    }

    protected static function forceSync(): bool {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    private function dbMap(): array {
        return [
            'id' => $this->value,
            'name' => json_encode(['en' => 'One']),
            'slug' => json_encode(['en' => 'one']),
            'type' => 'fake',
        ];
    }
}

function insertOrphanTag(): void {
    DB::table('tags')->insert([
        'id' => 99,
        'name' => json_encode(['en' => 'Orphan']),
        'slug' => json_encode(['en' => 'orphan']),
        'type' => 'fake',
    ]);
}

it('upserts every case into the resolved table', function () {
    SeedDbStrictFixture::sync();

    expect(DB::table('tags')->where('id', 1)->where('type', 'fake')->exists())->toBeTrue();
});

it('throws when orphaned rows exist and force sync is disabled', function () {
    SeedDbStrictFixture::sync();
    insertOrphanTag();

    expect(fn () => SeedDbStrictFixture::sync())->toThrow(RuntimeException::class);
});

it('deletes orphaned rows when force sync is enabled', function () {
    SeedDbForcedFixture::sync();
    insertOrphanTag();

    SeedDbForcedFixture::sync();

    expect(DB::table('tags')->where('id', 99)->exists())->toBeFalse();
});
