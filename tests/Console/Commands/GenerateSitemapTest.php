<?php

declare(strict_types=1);

use App\Models\BlogArticle;
use App\Models\Project;
use Illuminate\Support\Facades\File;

use function Pest\Laravel\artisan;
use function Safe\file_get_contents;
use function Safe\mkdir;

beforeEach(function (): void {
    $this->public_directory = \sys_get_temp_dir().'/sitemap_'.\uniqid();
    mkdir($this->public_directory);
    app()->usePublicPath($this->public_directory);
});

afterEach(function (): void {
    File::deleteDirectory($this->public_directory);
});

describe('app:generate-sitemap', function (): void {
    it('writes the sitemap with the static pages and the crawlable content', function (): void {
        $article = BlogArticle::factory()->published()->create();
        $draft = BlogArticle::factory()->draft()->create();
        $project = Project::factory()->published()->create();
        $sitemap_path = public_path('sitemap.xml');

        expect(File::exists($sitemap_path))->toBeFalse();

        artisan('app:generate-sitemap')->assertExitCode(0);

        $sitemap = file_get_contents($sitemap_path);

        expect(File::exists($this->public_directory.'/sitemap.xml'))->toBeTrue()
            ->and($sitemap)->toContain('<urlset')
            ->and($sitemap)->toContain(route('home'))
            ->and($sitemap)->toContain($article->getTranslation('slug', 'en'))
            ->and($sitemap)->toContain($project->getTranslation('slug', 'en'))
            ->and($sitemap)->not->toContain($draft->getTranslation('slug', 'en'));
    });
});
