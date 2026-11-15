<?php

declare(strict_types=1);

use App\Models\BlogArticle;
use App\Models\Project;

afterEach(function () {
    $path = public_path('sitemap.xml');

    if (file_exists($path)) {
        unlink($path);
    }
});

it('generates the sitemap file without errors', function () {
    // Exercises the crawlable-article path, which would throw on the old
    // `news.show` route bug.
    BlogArticle::factory()->published()->count(2)->create();
    BlogArticle::factory()->draft()->create();
    Project::factory()->published()->create();

    $this->artisan('app:generate-sitemap')->assertSuccessful();

    $path = public_path('sitemap.xml');

    expect(file_exists($path))->toBeTrue()
        ->and(file_get_contents($path))->toContain('<urlset');
});
