<?php

declare(strict_types=1);

use App\Filament\Pages\ManageBlogSettings;
use App\Filament\Resources\BlogArticles\BlogArticleResource;
use App\Settings\BlogSettings;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    authenticateAsAdmin();
});

describe('access', function (): void {
    it('renders the blog settings page for an admin', function (): void {
        livewire(ManageBlogSettings::class)->assertOk();
    });

    it('forbids users who cannot access the panel', function (): void {
        assertPanelUrlIsForbiddenForNonAdminUser(ManageBlogSettings::getUrl());
    });

    it('redirects guests to the login page', function (): void {
        assertPanelUrlRedirectsGuestToLogin(ManageBlogSettings::getUrl());
    });
});

describe('saving', function (): void {
    it('saves the author bio for both locales', function (): void {
        $expected_bio = ['en' => 'English bio', 'it' => 'Bio italiana'];

        expect(resolve(BlogSettings::class)->author_bio)->not->toBe($expected_bio);

        livewire(ManageBlogSettings::class)
            ->fillForm(['author_bio' => $expected_bio])
            ->call('save')
            ->assertNotified()
            ->assertHasNoFormErrors();

        expect(resolve(BlogSettings::class)->author_bio)->toBe($expected_bio);
    });
});

describe('navigation', function (): void {
    it('nests the settings item under the blog articles navigation item', function (): void {
        $blog_item = collect(Filament::getNavigation())
            ->flatMap(fn (NavigationGroup $group) => $group->getItems())
            ->first(fn (NavigationItem $item): bool => $item->getLabel() === BlogArticleResource::getNavigationLabel());

        expect($blog_item)->not->toBeNull()
            ->and($blog_item->getChildItems())->toHaveCount(1)
            ->and($blog_item->getChildItems()[0]->getLabel())->toBe(ManageBlogSettings::getNavigationLabel());
    });
});
