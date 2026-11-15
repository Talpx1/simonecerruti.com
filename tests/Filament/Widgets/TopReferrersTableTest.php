<?php

declare(strict_types=1);

use App\Filament\Widgets\TopReferrersTable;
use App\Models\VisitSession;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    authenticateAsAdmin();
});

it('lists the external referrers and leaves out direct visits', function (): void {
    $session = VisitSession::factory()->fromSocial('instagram')->create();
    VisitSession::factory()->direct()->create();

    livewire(TopReferrersTable::class)
        ->assertSuccessful()
        ->assertCountTableRecords(1)
        ->assertSee($session->referrer_host);
});

it('lists no referrers when there are no visits', function (): void {
    livewire(TopReferrersTable::class)
        ->assertSuccessful()
        ->assertCountTableRecords(0);
});
