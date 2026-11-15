<?php

declare(strict_types=1);

use App\Filament\Widgets\TopPagesTable;
use App\Models\PageView;
use App\Models\VisitSession;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    authenticateAsAdmin();
});

it('lists each viewed path once with its views', function (): void {
    $session = VisitSession::factory()->create();
    PageView::factory()->count(2)->create([
        'visit_session_id' => $session->id,
        'url_path' => '/popular',
    ]);

    livewire(TopPagesTable::class)
        ->assertSuccessful()
        ->assertCountTableRecords(1)
        ->assertSee('/popular');
});

it('lists no pages when there are no page views', function (): void {
    livewire(TopPagesTable::class)
        ->assertSuccessful()
        ->assertCountTableRecords(0);
});
