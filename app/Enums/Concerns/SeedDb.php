<?php

declare(strict_types=1);

namespace App\Enums\Concerns;

use Illuminate\Support\Facades\DB;

trait SeedDb {
    private static function guessTable(): string {
        if (method_exists(static::class, 'getOrGuessModelClass')) {
            $class = static::getOrGuessModelClass();

            return (new $class)->getTable();
        }

        return str(class_basename(__CLASS__))->snake()->lower()->toString();
    }

    private static function getOrGuessTable(): string {
        if (method_exists(static::class, 'table')) {
            return static::table();
        }

        return static::guessTable();
    }

    public static function sync(): void {
        $mapped = array_map(fn (self $case) => $case->dbMap(), static::cases());

        DB::table(static::getOrGuessTable())->upsert(
            $mapped,
            static::upsertKeys(),
        );

        if (static::partialSync()) {
            return;
        }

        $current_keys = array_column($mapped, static::upsertKeys()[0]);

        $orphans = DB::table(static::getOrGuessTable())
            ->whereNotIn(static::upsertKeys()[0], $current_keys)
            ->get();

        if ($orphans->isNotEmpty() && ! static::forceSync()) {
            throw new \RuntimeException(
                'Cannot sync: orphaned rows exist ['.$orphans->pluck(static::upsertKeys()[0])->implode(', ').']. Override forceSync() to delete anyway.'
            );
        }

        DB::table(static::getOrGuessTable())
            ->whereNotIn(static::upsertKeys()[0], $current_keys)
            ->delete();
    }

    protected static function forceSync(): bool {
        return false;
    }

    /**
     * If true, sync() only handles enum's records
     * and ignores the ones created via CRUD.
     */
    protected static function partialSync(): bool {
        return false;
    }

    /** @return string[] */
    protected static function upsertKeys(): array {
        return ['id'];
    }

    /**
     * @return array<string, mixed>
     */
    abstract private function dbMap(): array;
}
