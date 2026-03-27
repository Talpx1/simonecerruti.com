<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localize', 'localeSessionRedirect', 'localizationRedirect'],
], function () {
    Route::livewire('/', \App\Livewire\Pages\Home::class)->name('home');

    Route::livewireLocalized('contacts', \App\Livewire\Pages\Contacts::class)->name('contacts');

    Route::livewireLocalized('about', \App\Livewire\Pages\About::class)->name('about');

    Route::livewireLocalized('how-i-work', \App\Livewire\Pages\HowIWork::class)->name('how_i_work');

    Route::livewireLocalized('projects', \App\Livewire\Pages\Project\ProjectList::class)->name('projects');

    Route::livewireLocalized('project-show', \App\Livewire\Pages\Project\ProjectShow::class)->name('project.show');

    Route::livewireLocalized('blog', \App\Livewire\Pages\BlogArticle\BlogArticleList::class)->name('blog');

    Route::livewireLocalized('blog-article-show', \App\Livewire\Pages\BlogArticle\BlogArticleShow::class)->name('blog_article.show');

    Route::livewireLocalized('privacy-policy', \App\Livewire\Pages\PrivacyPolicy::class)->name('privacy_policy');

    Route::livewireLocalized('cookie-policy', \App\Livewire\Pages\CookiePolicy::class)->name('cookie_policy');

    Route::livewireLocalized('terms-and-conditions', \App\Livewire\Pages\TermsAndConditions::class)->name('terms_and_conditions');
});
