<?php

declare(strict_types=1);

use App\Models\Tag;
use Tests\Fixtures\Enums\ForceSeedableRows;
use Tests\Fixtures\Enums\PartiallySeedableRows;
use Tests\Fixtures\Enums\SeedableRows;

describe('sync', function (): void {
    it('upserts every case into the resolved table', function (): void {
        $count = fn (): int => Tag::query()->where('type', SeedableRows::TYPE)->count();

        expect($count())->toBe(0)
            ->and(SeedableRows::sync())->toBeNull()
            ->and($count())->toBe(\count(SeedableRows::cases()));
    });

    it('does not duplicate the rows when synced twice', function (): void {
        $count = fn (): int => Tag::query()->where('type', SeedableRows::TYPE)->count();

        SeedableRows::sync();

        expect($count())->toBe(\count(SeedableRows::cases()))
            ->and(SeedableRows::sync())->toBeNull()
            ->and($count())->toBe(\count(SeedableRows::cases()));
    });

    it('throws when orphaned rows exist and force sync is disabled', function (): void {
        SeedableRows::sync();
        insertOrphanTag(99, SeedableRows::TYPE);

        expect(fn () => SeedableRows::sync())
            ->toThrow(RuntimeException::class, 'Cannot sync: orphaned rows exist [99]');
    });

    it('deletes orphaned rows when force sync is enabled', function (): void {
        ForceSeedableRows::sync();
        insertOrphanTag(99, ForceSeedableRows::TYPE);

        expect(Tag::query()->whereKey(99)->exists())->toBeTrue()
            ->and(ForceSeedableRows::sync())->toBeNull()
            ->and(Tag::query()->whereKey(99)->exists())->toBeFalse()
            ->and(Tag::query()->where('type', ForceSeedableRows::TYPE)->count())->toBe(\count(ForceSeedableRows::cases()));
    });

    it('keeps rows outside of the enum when partial sync is enabled', function (): void {
        insertOrphanTag(99, PartiallySeedableRows::TYPE);

        expect(Tag::query()->whereKey(99)->exists())->toBeTrue()
            ->and(PartiallySeedableRows::sync())->toBeNull()
            ->and(Tag::query()->whereKey(99)->exists())->toBeTrue()
            ->and(Tag::query()->whereKey(PartiallySeedableRows::ONE->value)->exists())->toBeTrue();
    });
});
