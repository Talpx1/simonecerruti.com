<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],
], function () {
    Route::livewire('/', \App\Livewire\Pages\Home::class)->name('home');
    Route::livewire('/contacts', \App\Livewire\Pages\Contacts::class)->name('contacts');
    Route::livewire('/about', \App\Livewire\Pages\About::class)->name('about');
});
