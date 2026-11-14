<?php

declare(strict_types=1);

use App\Enums\ServiceAreas;
use App\Filament\Resources\Tags\Pages\CreateTag;
use App\Filament\Resources\Tags\Pages\EditTag;
use App\Filament\Resources\Tags\Pages\ListTags;
use App\Models\Tag;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->withoutVite();
    actingAsAdmin();
});

it('lists tags', function () {
    $tags = collect(['Alpha', 'Beta', 'Gamma'])
        ->map(fn (string $name) => Tag::findOrCreate($name));

    livewire(ListTags::class)
        ->assertCanSeeTableRecords($tags);
});

it('creates a tag', function () {
    livewire(CreateTag::class)
        ->fillForm([
            'name' => 'Brand New Tag',
            'slug' => 'brand-new-tag',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Tag::query()->count())->toBe(1);
});

it('rejects a duplicate slug', function () {
    Tag::create([
        'name' => ['en' => 'Existing', 'it' => 'Existing'],
        'slug' => ['en' => 'existing', 'it' => 'existing'],
    ]);

    livewire(CreateTag::class)
        ->fillForm([
            'name' => 'New Tag',
            'slug' => 'existing',
        ])
        ->call('create')
        ->assertHasFormErrors(['slug']);
});

it('saves the description and the service of a tag', function () {
    $tag = Tag::findOrCreate('Laravel');

    livewire(EditTag::class, ['record' => $tag->id])
        ->fillForm([
            'description' => 'Framework PHP',
            'service' => ServiceAreas::WEB_ECOMMERCE_PLATFORMS->value,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $tag->refresh();

    expect($tag->getTranslation('description', 'it'))->toBe('Framework PHP')
        ->and($tag->service)->toBe(ServiceAreas::WEB_ECOMMERCE_PLATFORMS);
});
