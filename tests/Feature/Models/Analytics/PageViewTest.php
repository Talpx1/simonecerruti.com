<?php

declare(strict_types=1);

use App\Models\PageView;
use App\Models\VisitSession;
use Carbon\CarbonImmutable;

describe('casts', function () {
    it('casts viewed_at to immutable datetime', function () {
        $page_view = PageView::factory()->create();

        expect($page_view->viewed_at)->toBeInstanceOf(CarbonImmutable::class);
    });
});

describe('relationships', function () {
    it('belongs to a visit session', function () {
        $session = VisitSession::factory()->create();
        $page_view = PageView::factory()->create(['visit_session_id' => $session->id]);

        expect($page_view->visitSession)->toBeInstanceOf(VisitSession::class)
            ->and($page_view->visitSession->id)->toBe($session->id);
    });

    it('is cascade-deleted with its visit session', function () {
        $session = VisitSession::factory()->create();
        PageView::factory()->count(2)->create(['visit_session_id' => $session->id]);

        $session->delete();

        expect(PageView::query()->count())->toBe(0);
    });
});
