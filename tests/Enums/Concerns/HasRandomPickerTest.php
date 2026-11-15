<?php

declare(strict_types=1);

use Tests\Fixtures\Enums\Fruits;

describe('random', function (): void {
    it('returns one of the cases', function (): void {
        expect(Fruits::cases())->toContain(Fruits::random());
    });

    it('can return every case', function (): void {
        $picked = collect(\range(1, 200))->map(fn (): Fruits => Fruits::random())->unique();

        expect($picked)->toHaveSameSize(Fruits::cases());
    });
});
