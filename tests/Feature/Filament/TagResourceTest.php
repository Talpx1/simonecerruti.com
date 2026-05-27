<?php

declare(strict_types=1);

use App\Filament\Resources\Tags\Pages\CreateTag;
use App\Filament\Resources\Tags\Pages\ListTags;
use Spatie\Tags\Tag;

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
