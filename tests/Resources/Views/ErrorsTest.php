<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Route;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

describe('404 page', function (): void {
    it('renders the designed 404 page with the pixel canvas and the way back', function (): void {
        get('/this-page-does-not-exist')
            ->assertNotFound()
            ->assertSee('data-effect="not-found-particles"', escape: false)
            ->assertSee('data-code="404"', escape: false)
            ->assertSee('class="tag-cloud"', escape: false)
            ->assertSee('href="'.route('contacts').'"', escape: false);
    });

    it('renders the same 404 page for an authenticated user', function (): void {
        actingAs(User::factory()->create());

        get('/this-page-does-not-exist')
            ->assertNotFound()
            ->assertSee('data-code="404"', escape: false);
    });
});

describe('other error codes', function (): void {
    it('renders them on the same standalone layout', function (int $status): void {
        Route::get('/__error/'.$status, fn () => abort($status));

        get('/__error/'.$status)
            ->assertStatus($status)
            ->assertSee('data-code="'.$status.'"', escape: false)
            ->assertSee('class="page-hero"', escape: false)
            ->assertDontSee('hero-cta', escape: false);
    })->with([403, 500, 503]);
});
