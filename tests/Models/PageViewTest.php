<?php

declare(strict_types=1);

use App\Models\PageView;
use App\Models\VisitSession;
use Carbon\CarbonImmutable;

describe('attributes', function (): void {
    describe('viewed_at', function (): void {
        it('is cast to an immutable datetime', function (): void {
            $page_view = PageView::factory()->create();

            expect($page_view->viewed_at)->toBeInstanceOf(CarbonImmutable::class);
        });
    });
});

describe('relationships', function (): void {
    describe('visitSession', function (): void {
        it('belongs to a visit session', function (): void {
            $session = VisitSession::factory()->create();
            $page_view = PageView::factory()->for($session)->create();

            expect($page_view->visitSession)->toBeInstanceOf(VisitSession::class)
                ->and($page_view->visitSession->id)->toBe($session->id);
        });

        it('is cascade-deleted with its visit session', function (): void {
            $session = VisitSession::factory()->create();
            PageView::factory()->count(2)->for($session)->create();

            expect(PageView::query()->count())->toBe(2)
                ->and($session->delete())->toBeTrue()
                ->and(PageView::query()->count())->toBe(0);
        });
    });
});
