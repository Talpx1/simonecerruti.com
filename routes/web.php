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

    Route::livewireLocalized('projects', \App\Livewire\Pages\Projects::class)->name('projects');
});
