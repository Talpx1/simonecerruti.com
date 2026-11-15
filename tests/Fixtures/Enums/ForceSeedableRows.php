<?php

declare(strict_types=1);

namespace Tests\Fixtures\Enums;

use App\Enums\Concerns\SeedDb;

enum ForceSeedableRows: int {
    use SeedDb;

    case ONE = 1;
    case TWO = 2;

    public const string TYPE = 'force_seedable_rows';

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
            'name' => \json_encode(['en' => $this->name]),
            'slug' => \json_encode(['en' => \strtolower($this->name)]),
            'type' => self::TYPE,
        ];
    }
}
