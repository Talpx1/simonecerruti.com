<?php

declare(strict_types=1);

use App\Models\Project;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

describe('wherePublished scope', function () {
    it('only returns published projects', function () {
        $published = Project::factory()->published()->create();
        $unpublished = Project::factory()->unpublished()->create();

        $result = Project::query()->wherePublished()->get();

        expect($result->contains($published))->toBeTrue()
            ->and($result->contains($unpublished))->toBeFalse();
    });
});

describe('casts', function () {
    it('casts links to a collection', function () {
        $project = Project::factory()->create([
            'links' => [['url' => 'https://example.com']],
        ]);

        expect($project->links)->toBeInstanceOf(Collection::class)
            ->and($project->links->first()['url'])->toBe('https://example.com');
    });
});

describe('featured_image_url attribute', function () {
    it('falls back to the bundled placeholder when no media is attached', function () {
        $project = Project::factory()->create();

        expect($project->featured_image_url)->toBe(asset('images/fallback.jpg'));
    });

    it('returns a media url when a featured image is attached', function () {
        Storage::fake('public');

        $project = Project::factory()->create();
        $project->addMedia(UploadedFile::fake()->image('featured.jpg', 1920, 1080))
            ->toMediaCollection('featured_image');

        expect($project->refresh()->featured_image_url)->not->toBe(asset('images/fallback.jpg'));
    });
});

describe('short_description_or_excerpt attribute', function () {
    it('returns the short description when present', function () {
        $project = Project::factory()->create();

        withLocale('en', function () use ($project) {
            expect($project->short_description_or_excerpt)->toBe($project->short_description);
        });
    });
});
