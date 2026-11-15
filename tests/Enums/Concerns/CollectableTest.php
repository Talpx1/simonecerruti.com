<?php

declare(strict_types=1);

use Illuminate\Support\Collection;
use Tests\Fixtures\Enums\Fruits;

describe('collect', function (): void {
    it('returns a collection holding every case', function (): void {
        expect(Fruits::collect())->toBeInstanceOf(Collection::class)
            ->toHaveSameSize(Fruits::cases())
            ->and(Fruits::collect()->all())->toBe(Fruits::cases());
    });
});
