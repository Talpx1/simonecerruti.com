<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('home/HomePage');
})->name('home');

Route::get('/blog', function () {
    return Inertia::render('blog/BlogPage');
})->name('blog');

Route::resource('contact-lead', \App\Http\Controllers\ContactLeadController::class)->only('store');
