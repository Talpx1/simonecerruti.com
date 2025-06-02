<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('home/HomePage');
})->name('home');

Route::get('/about', function () {
    return Inertia::render('about/AboutPage');
})->name('about');

Route::get('/blog', function () {
    return Inertia::render('blog/BlogPage');
})->name('blog');

Route::get('/projects', function () {
    return Inertia::render('projects/ProjectsPage');
})->name('projects');
