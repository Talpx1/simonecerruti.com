<?php

declare(strict_types=1);

use App\Enums\BlogCategories;
use App\Livewire\Pages\Home;
use App\Models\Project;

use function Pest\Livewire\livewire;

beforeEach(fn () => $this->withoutVite());

it('renders the home page', function () {
    BlogCategories::sync();
    Project::factory()->published()->count(3)->create();
    Project::factory()->published()->featured()->create();

    livewire(Home::class)->assertOk();
});
