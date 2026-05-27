<?php

declare(strict_types=1);

use App\Livewire\Pages\About;
use App\Livewire\Pages\BlogArticle\BlogArticleList;
use App\Livewire\Pages\BlogArticle\BlogArticleShow;
use App\Livewire\Pages\Contacts;
use App\Livewire\Pages\CookiePolicy;
use App\Livewire\Pages\Home;
use App\Livewire\Pages\HowIWork;
use App\Livewire\Pages\PrivacyPolicy;
use App\Livewire\Pages\Project\ProjectList;
use App\Livewire\Pages\Project\ProjectShow;
use App\Livewire\Pages\TagArchive;
use App\Livewire\Pages\TermsAndConditions;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localize', 'localeSessionRedirect', 'localizationRedirect'],
], function () {
    Route::livewire('/', Home::class)->name('home');

    Route::livewireLocalized('contacts', Contacts::class)->name('contacts');

    Route::livewireLocalized('about', About::class)->name('about');

    Route::livewireLocalized('how-i-work', HowIWork::class)->name('how_i_work');

    Route::livewireLocalized('projects', ProjectList::class)->name('projects');

    Route::livewireLocalized('project-show', ProjectShow::class)->name('project.show');

    Route::livewireLocalized('blog', BlogArticleList::class)->name('blog');

    Route::livewireLocalized('blog-article-show', BlogArticleShow::class)->name('blog_article.show');

    Route::livewireLocalized('privacy-policy', PrivacyPolicy::class)->name('privacy_policy');

    Route::livewireLocalized('cookie-policy', CookiePolicy::class)->name('cookie_policy');

    Route::livewireLocalized('terms-and-conditions', TermsAndConditions::class)->name('terms_and_conditions');

    Route::livewireLocalized('tag-archive', TagArchive::class)->name('tag_archive');
});
