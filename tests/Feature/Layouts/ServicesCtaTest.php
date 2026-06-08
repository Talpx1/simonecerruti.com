<?php

declare(strict_types=1);

use App\Enums\BlogCategories;
use App\Livewire\Pages\BlogArticle\BlogArticleShow;
use App\Livewire\Pages\Home;
use App\Livewire\Pages\Project\ProjectShow;
use App\Models\BlogArticle;
use App\Models\Project;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;

use function Pest\Livewire\livewire;

beforeEach(fn () => $this->withoutVite());

it('exposes a services CTA on the home page', function () {
    BlogCategories::sync();

    livewire(Home::class)
        ->assertOk()
        ->assertSeeHtml('data-pan="cta-home-services"');
});

it('exposes a services CTA on the standalone pages', function (string $route, string $pan) {
    $this->withoutMiddleware([
        LaravelLocalizationRedirectFilter::class,
        LocaleSessionRedirect::class,
    ])->get(route($route))
        ->assertOk()
        ->assertSee('data-pan="'.$pan.'"', escape: false);
})->with([
    'how I work' => ['how_i_work', 'cta-method-services'],
    'projects' => ['projects', 'cta-projects-services'],
]);

it('exposes a services CTA on the project show page', function () {
    livewire(ProjectShow::class, ['project' => Project::factory()->published()->create()])
        ->assertOk()
        ->assertSeeHtml('data-pan="cta-project-services"');
});

it('exposes a services CTA on the blog article show page', function () {
    livewire(BlogArticleShow::class, ['blog_article' => BlogArticle::factory()->published()->create()])
        ->assertOk()
        ->assertSeeHtml('data-pan="cta-blog-article-services"');
});

// Guards that the two refactors keep rendering the expected markup: the shared
// cta-section still emits the how-i-work headline, and the about page now emits
// the social links through the shared <x-social-links> component.
it('keeps the refactored markup intact', function (string $route, string $needle) {
    $this->withoutMiddleware([
        LaravelLocalizationRedirectFilter::class,
        LocaleSessionRedirect::class,
    ])->get(route($route))
        ->assertOk()
        ->assertSee(__($needle));
})->with([
    'how-i-work cta-section heading' => ['how_i_work', "You don't need faster software."],
    'about social component' => ['about', 'cta-social-linkedin-about'],
]);
