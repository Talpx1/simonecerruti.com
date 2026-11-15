<?php

declare(strict_types=1);

use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

it('moves the legacy files and reports how many media items were moved', function (): void {
    Storage::fake('public');
    $model_id = (string) Str::uuid7();

    DB::table('media')->insert([
        'model_type' => Project::class,
        'model_id' => $model_id,
        'legacy_model_id' => 5,
        'uuid' => (string) Str::uuid(),
        'collection_name' => 'gallery',
        'name' => 'a',
        'file_name' => 'a.jpg',
        'disk' => 'public',
        'size' => 1,
        'manipulations' => '[]',
        'custom_properties' => '[]',
        'generated_conversions' => '[]',
        'responsive_images' => '[]',
    ]);
    Storage::disk('public')->put('projects/5/gallery/a.jpg', 'a');

    $this->artisan('media:move-legacy-files')
        ->expectsOutput('Moved 1 media item(s).')
        ->assertSuccessful();

    expect(Storage::disk('public')->exists("projects/{$model_id}/gallery/a.jpg"))->toBeTrue();
});

it('reports zero when there is nothing to move', function (): void {
    $this->artisan('media:move-legacy-files')
        ->expectsOutput('Moved 0 media item(s).')
        ->assertSuccessful();
});
