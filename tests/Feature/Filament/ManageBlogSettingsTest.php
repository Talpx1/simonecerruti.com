<?php

declare(strict_types=1);

use App\Filament\Pages\ManageBlogSettings;
use App\Filament\Resources\BlogArticles\BlogArticleResource;
use App\Settings\BlogSettings;
use Filament\Facades\Filament;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->withoutVite();
});

it('renders the blog settings page for an admin', function () {
    actingAsAdmin();

    livewire(ManageBlogSettings::class)->assertOk();
});

it('saves the author bio for both locales', function () {
    actingAsAdmin();

    livewire(ManageBlogSettings::class)
        ->fillForm([
            'author_bio' => ['en' => 'English bio', 'it' => 'Bio italiana'],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $settings = resolve(BlogSettings::class);

    expect($settings->author_bio)->toBe(['en' => 'English bio', 'it' => 'Bio italiana']);
});

it('redirects guests to the login page', function () {
    $this->get(ManageBlogSettings::getUrl())->assertRedirect();
});

it('nests the settings item under the blog articles navigation item', function () {
    actingAsAdmin();

    $blog_item = collect(Filament::getNavigation())
        ->flatMap(fn ($group) => $group->getItems())
        ->first(fn ($item) => $item->getLabel() === BlogArticleResource::getNavigationLabel());

    expect($blog_item)->not->toBeNull()
        ->and($blog_item->getChildItems())->toHaveCount(1)
        ->and($blog_item->getChildItems()[0]->getLabel())->toBe(ManageBlogSettings::getNavigationLabel());
});
