<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\BlogArticle;
use App\Models\Project;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-sitemap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the sitemap.';

    /**
     * Execute the console command.
     */
    public function handle(): void {
        $sitemap = Sitemap::create();

        $sitemap = $this->addStaticUrls($sitemap);

        $sitemap->add(BlogArticle::all());
        $sitemap->add(Project::all());

        $sitemap->writeToFile(public_path('sitemap.xml'));
    }

    private function addStaticUrls(Sitemap $sitemap): Sitemap {
        /** @var string[] */
        $locales = array_keys(App::supportedLocales());

        $routes = [
            ['route' => route('home'), 'change_freq' => Url::CHANGE_FREQUENCY_MONTHLY, 'priority' => 1],
            ['route' => route('contacts'), 'change_freq' => Url::CHANGE_FREQUENCY_NEVER, 'priority' => 0.1],
            ['route' => route('about'), 'change_freq' => Url::CHANGE_FREQUENCY_NEVER, 'priority' => 0.1],
            ['route' => route('how_i_work'), 'change_freq' => Url::CHANGE_FREQUENCY_NEVER, 'priority' => 0.1],
            ['route' => route('projects'), 'change_freq' => Url::CHANGE_FREQUENCY_MONTHLY, 'priority' => 0.5],
            ['route' => route('blog'), 'change_freq' => Url::CHANGE_FREQUENCY_MONTHLY, 'priority' => 0.5],
        ];

        foreach ($locales as $locale) {
            foreach ($routes as $route) {
                /** @var \Illuminate\Support\Uri */
                $uri = Route::localizedUrl($locale, $route['route']);

                $url = Url::create($uri->__toString())
                    ->setPriority($route['priority'])
                    ->setChangeFrequency($route['change_freq']);

                $url = $this->attachAlternates($url, $locale, $route['route']);

                $sitemap->add($url);
            }
        }

        return $sitemap;
    }

    private function attachAlternates(Url $url, string $current_locale, string $route): Url {
        /** @var string[] */
        $locales = array_keys(App::supportedLocales());

        foreach ($locales as $alternate) {
            if ($alternate === $current_locale) {
                continue;
            }

            /** @var \Illuminate\Support\Uri */
            $uri = Route::localizedUrl($alternate, $route);
            $url->addAlternate($uri->__toString(), $alternate);
        }

        return $url;
    }
}
