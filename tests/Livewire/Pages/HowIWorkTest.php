<?php

declare(strict_types=1);

use App\Livewire\Pages\HowIWork;

use function Pest\Livewire\livewire;

it('renders the page with the interactive demos and their config', function (): void {
    livewire(HowIWork::class)
        ->assertOk()
        ->assertSeeHtml('data-effect="how-i-work"')
        ->assertSeeHtml('id="method-fit-section"')
        ->assertSeeHtml('class="page-effect-config"')
        ->assertSee(__('Orders after 5 pm'))
        ->assertSeeHtml('data-pan="cta-method-contacts"')
        ->assertSeeHtml('data-pan="cta-method-services"')
        ->assertSeeHtml('data-pan="cta-method-projects"');
});

it('leaves the page indexable', function (): void {
    assertPageIsIndexable('how_i_work');
});
