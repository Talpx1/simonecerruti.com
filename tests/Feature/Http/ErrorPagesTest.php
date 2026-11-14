<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

it('renders the designed 404 page with the pixel canvas and the way back', function () {
    $this->get('/this-page-does-not-exist')
        ->assertNotFound()
        ->assertSee('data-fx="nf"', escape: false)
        ->assertSee('data-code="404"', escape: false)
        ->assertSee('class="tagcloud"', escape: false)
        ->assertSee('href="'.route('contacts').'"', escape: false);
});

it('renders the other error codes on the same standalone layout', function (int $status) {
    Route::get('/__error/'.$status, fn () => abort($status));

    $this->get('/__error/'.$status)
        ->assertStatus($status)
        ->assertSee('data-code="'.$status.'"', escape: false)
        ->assertSee('class="page-hero"', escape: false)
        ->assertDontSee('hero-cta', escape: false);
})->with([403, 500, 503]);
