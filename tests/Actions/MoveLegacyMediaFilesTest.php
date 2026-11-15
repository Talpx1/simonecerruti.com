<?php

declare(strict_types=1);

use App\Actions\MoveLegacyMediaFiles;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

function insertLegacyMedia(string $file_name, string $model_id, int $legacy_model_id, string $collection = 'gallery'): void {
    DB::table('media')->insert([
        'model_type' => Project::class,
        'model_id' => $model_id,
        'legacy_model_id' => $legacy_model_id,
        'uuid' => (string) Str::uuid(),
        'collection_name' => $collection,
        'name' => $file_name,
        'file_name' => $file_name,
        'disk' => 'public',
        'size' => 1,
        'manipulations' => '[]',
        'custom_properties' => '[]',
        'generated_conversions' => '[]',
        'responsive_images' => '[]',
    ]);
}

beforeEach(function (): void {
    Storage::fake('public');
});

it('moves the files, conversions and responsive images to the uuid path and clears the legacy id', function (): void {
    $model_id = (string) Str::uuid7();
    insertLegacyMedia('a.jpg', $model_id, 5);
    insertLegacyMedia('b.jpg', $model_id, 5);

    Storage::disk('public')->put('projects/5/gallery/a.jpg', 'a');
    Storage::disk('public')->put('projects/5/gallery/b.jpg', 'b');
    Storage::disk('public')->put('projects/5/gallery/conversions/a-thumb.jpg', 'thumb');
    Storage::disk('public')->put('projects/5/gallery/responsive/a___a_100_100.jpg', 'resp');

    $moved_count = MoveLegacyMediaFiles::run();

    expect($moved_count)->toBe(2)
        ->and(Storage::disk('public')->allFiles('projects/5'))->toBeEmpty()
        ->and(Storage::disk('public')->exists("projects/{$model_id}/gallery/a.jpg"))->toBeTrue()
        ->and(Storage::disk('public')->exists("projects/{$model_id}/gallery/b.jpg"))->toBeTrue()
        ->and(Storage::disk('public')->exists("projects/{$model_id}/gallery/conversions/a-thumb.jpg"))->toBeTrue()
        ->and(Storage::disk('public')->exists("projects/{$model_id}/gallery/responsive/a___a_100_100.jpg"))->toBeTrue()
        ->and(Media::query()->whereNotNull('legacy_model_id')->count())->toBe(0);
});

it('keeps media of different owners and collections apart', function (): void {
    $first_id = (string) Str::uuid7();
    $second_id = (string) Str::uuid7();
    insertLegacyMedia('a.jpg', $first_id, 5);
    insertLegacyMedia('b.jpg', $second_id, 6, 'featured_image');

    Storage::disk('public')->put('projects/5/gallery/a.jpg', 'a');
    Storage::disk('public')->put('projects/6/featured_image/b.jpg', 'b');

    expect(MoveLegacyMediaFiles::run())->toBe(2)
        ->and(Storage::disk('public')->exists("projects/{$first_id}/gallery/a.jpg"))->toBeTrue()
        ->and(Storage::disk('public')->exists("projects/{$second_id}/featured_image/b.jpg"))->toBeTrue();
});

it('is idempotent and resumes after an interrupted run', function (): void {
    $model_id = (string) Str::uuid7();
    insertLegacyMedia('a.jpg', $model_id, 5);

    Storage::disk('public')->put('projects/5/gallery/a.jpg', 'a');
    Storage::disk('public')->put("projects/{$model_id}/gallery/a.jpg", 'a');

    expect(MoveLegacyMediaFiles::run())->toBe(1)
        ->and(MoveLegacyMediaFiles::run())->toBe(0)
        ->and(Storage::disk('public')->exists("projects/{$model_id}/gallery/a.jpg"))->toBeTrue()
        ->and(Storage::disk('public')->exists('projects/5/gallery/a.jpg'))->toBeFalse();
});

it('does nothing when no media has a legacy id', function (): void {
    expect(MoveLegacyMediaFiles::run())->toBe(0);
});
