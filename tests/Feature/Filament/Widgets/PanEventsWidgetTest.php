<?php

declare(strict_types=1);

use App\Filament\Widgets\PanEventsWidget;
use Illuminate\Support\Facades\DB;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->withoutVite();
    actingAsAdmin();
});

it('categorizes events by prefix in the table', function () {
    DB::table('pan_analytics')->insert([
        ['name' => 'cta-nav-home', 'impressions' => 12, 'hovers' => 3, 'clicks' => 4],
        ['name' => 'cta-social-instagram', 'impressions' => 8, 'hovers' => 1, 'clicks' => 2],
        ['name' => 'section-impression-hero', 'impressions' => 50, 'hovers' => 0, 'clicks' => 0],
        ['name' => 'card-project-click', 'impressions' => 0, 'hovers' => 0, 'clicks' => 7],
    ]);

    livewire(PanEventsWidget::class)
        ->assertSuccessful()
        ->assertSee(__('Navigation'))
        ->assertSee(__('Social'))
        ->assertSee(__('Section impressions'))
        ->assertSee(__('Card clicks'))
        ->assertSee('cta-nav-home')
        ->assertSee('card-project-click');
});

it('renders the empty state when no events exist', function () {
    livewire(PanEventsWidget::class)
        ->assertSuccessful()
        ->assertSee(__('No events tracked yet.'));
});

it('renders rows when events exist', function () {
    DB::table('pan_analytics')->insert([
        ['name' => 'cta-nav-blog', 'impressions' => 1, 'hovers' => 0, 'clicks' => 2],
    ]);

    livewire(PanEventsWidget::class)
        ->assertSuccessful()
        ->assertSee('cta-nav-blog');
});
