<?php

declare(strict_types=1);

use App\Enums\ServiceAreas;
use App\Enums\TagTypes;
use App\Filament\Resources\Tags\Pages\CreateTag;
use App\Filament\Resources\Tags\Pages\EditTag;
use App\Filament\Resources\Tags\Pages\ListTags;
use App\Filament\Resources\Tags\TagResource;
use App\Models\Tag;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    authenticateAsAdmin();
});

describe('access', function (): void {
    it('forbids users who cannot access the panel', function (): void {
        $tag = Tag::findOrCreate('Laravel', TagTypes::TAG->value);

        assertPanelUrlIsForbiddenForNonAdminUser(TagResource::getUrl('index'));
        assertPanelUrlIsForbiddenForNonAdminUser(TagResource::getUrl('create'));
        assertPanelUrlIsForbiddenForNonAdminUser(TagResource::getUrl('edit', ['record' => $tag]));
    });

    it('redirects guests to the login page', function (): void {
        $tag = Tag::findOrCreate('Laravel', TagTypes::TAG->value);

        assertPanelUrlRedirectsGuestToLogin(TagResource::getUrl('index'));
        assertPanelUrlRedirectsGuestToLogin(TagResource::getUrl('create'));
        assertPanelUrlRedirectsGuestToLogin(TagResource::getUrl('edit', ['record' => $tag]));
    });
});

describe('list page', function (): void {
    it('lists tags', function (): void {
        $tags = collect(['Alpha', 'Beta', 'Gamma'])
            ->map(fn (string $name): Tag => Tag::findOrCreate($name, TagTypes::TAG->value));

        livewire(ListTags::class)
            ->assertCanSeeTableRecords($tags);
    });
});

describe('create page', function (): void {
    it('creates a tag', function (): void {
        expect(Tag::query()->count())->toBe(0);

        livewire(CreateTag::class)
            ->fillForm([
                'name' => 'Brand New Tag',
                'slug' => 'brand-new-tag',
            ])
            ->call('create')
            ->assertNotified()
            ->assertHasNoFormErrors();

        expect(Tag::query()->count())->toBe(1);
    });

    it('validates the required fields', function (): void {
        livewire(CreateTag::class)
            ->fillForm([
                'name' => null,
                'slug' => null,
            ])
            ->call('create')
            ->assertHasFormErrors([
                'name' => 'required',
                'slug' => 'required',
            ])
            ->assertNotNotified();

        expect(Tag::query()->count())->toBe(0);
    });

    it('rejects a duplicate slug', function (): void {
        Tag::query()->create([
            'name' => ['en' => 'Existing', 'it' => 'Existing'],
            'slug' => ['en' => 'existing', 'it' => 'existing'],
        ]);

        livewire(CreateTag::class)
            ->fillForm([
                'name' => 'New Tag',
                'slug' => 'existing',
            ])
            ->call('create')
            ->assertHasFormErrors(['slug'])
            ->assertNotNotified();

        expect(Tag::query()->count())->toBe(1);
    });
});

describe('edit page', function (): void {
    it('saves the description and the service of a tag', function (): void {
        $tag = Tag::findOrCreate('Laravel', TagTypes::TAG->value);

        expect($tag->service)->toBeNull();

        livewire(EditTag::class, ['record' => $tag->id])
            ->fillForm([
                'description' => 'Framework PHP',
                'service' => ServiceAreas::WEB_ECOMMERCE_PLATFORMS->value,
            ])
            ->call('save')
            ->assertNotified()
            ->assertHasNoFormErrors();

        $tag->refresh();

        expect($tag->getTranslation('description', 'it'))->toBe('Framework PHP')
            ->and($tag->service)->toBe(ServiceAreas::WEB_ECOMMERCE_PLATFORMS);
    });
});
