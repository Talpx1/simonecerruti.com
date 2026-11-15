<?php

declare(strict_types=1);

namespace Tests\Fixtures\Enums;

use App\Enums\Concerns\SeedDb;

enum PartiallySeedableRows: int {
    use SeedDb;

    case ONE = 1;

    public const string TYPE = 'partially_seedable_rows';

    public static function table(): string {
        return 'tags';
    }

    protected static function partialSync(): bool {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    private function dbMap(): array {
        return [
            'id' => $this->value,
            'name' => \json_encode(['en' => $this->name]),
            'slug' => \json_encode(['en' => \strtolower($this->name)]),
            'type' => self::TYPE,
        ];
    }
}
