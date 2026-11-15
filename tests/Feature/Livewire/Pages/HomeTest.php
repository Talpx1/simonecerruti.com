<?php

declare(strict_types=1);

use App\Enums\TagTypes;
use App\Livewire\Pages\Home;
use App\Models\Project;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->withoutVite();
    app()->setLocale('en');
});

it('renders the home page', function () {
    Project::factory()->published()->count(3)->create();
    Project::factory()->published()->featured()->create();

    livewire(Home::class)
        ->assertOk()
        ->assertSee('data-effect="home"', escape: false)
        ->assertSee('id="home-webgl-canvas"', escape: false);
});

it('links the services to their landing pages', function () {
    livewire(Home::class)
        ->assertSee(route('services.management_erp_crm'), escape: false)
        ->assertSee(route('services.web_ecommerce_platforms'), escape: false);
});

it('does not render the blog section anymore', function () {
    livewire(Home::class)->assertDontSee('home-blog-heading', escape: false);
});

it('shows only the three most relevant published projects, featured first', function () {
    $featured = Project::factory()->published()->featured()->create(['title' => ['en' => 'Featured work', 'it' => 'Lavoro in evidenza']]);
    Project::factory()->published()->count(3)->create();
    $unpublished = Project::factory()->unpublished()->create(['title' => ['en' => 'Hidden work', 'it' => 'Lavoro nascosto']]);

    livewire(Home::class)
        ->assertSee($featured->title)
        ->assertDontSee($unpublished->title)
        ->assertSeeInOrder([$featured->title, 'Open the work'])
        ->assertViewHas('works', fn (array $works): bool => \count($works) === 3 && $works[0]['title'] === 'Featured work');
});

it('hides the works section when there are no projects', function () {
    livewire(Home::class)
        ->assertViewHas('works', [])
        ->assertDontSee('class="home-works"', escape: false);
});

it('shows the project kind and home summary when filled', function () {
    Project::factory()->published()->create([
        'kind' => ['en' => 'Custom CRM', 'it' => 'CRM su misura'],
        'home_summary' => ['en' => '<p>Before it was chaos. <strong>Now it is one tool.</strong></p>', 'it' => '<p>Prima caos.</p>'],
    ]);

    livewire(Home::class)
        ->assertSee('Custom CRM')
        ->assertSee('Before it was chaos.')
        ->assertSee('<strong>Now it is one tool.</strong>', escape: false);
});

it('falls back to the tag names as kind and hides the summary when empty', function () {
    $project = Project::factory()->published()->create();
    $project->attachTag('Laravel', TagTypes::TAG->value);

    livewire(Home::class)
        ->assertSee('Laravel')
        ->assertViewHas('works', fn (array $works): bool => $works[0]['summary_html'] === null);
});

it('renders the Italian version of the home page', function () {
    app()->setLocale('it');

    livewire(Home::class)
        ->assertOk()
        ->assertSee('Parliamone');
});
