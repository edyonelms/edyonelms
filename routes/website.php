<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('components.website.index');
})->name('website.home');

Route::prefix('web')->group(function () {

    Route::get('/about', fn() => view('components.website.about'))
        ->name('website.about');

    Route::get('/features', fn() => view('components.website.features'))
        ->name('website.features');

    Route::get('/pricing', fn() => view('components.website.pricing'))
        ->name('website.pricing');

    Route::get('/contact', fn() => view('components.website.contact'))
        ->name('website.contact');

    Route::get('/privacy', fn() => view('components.website.privacy-policy'))
        ->name('website.privacy');

    Route::get('/refund-policy', fn() => view('components.website.refund-policy'))
        ->name('website.refund-policy');

    Route::get('/terms-conditions', fn() => view('components.website.terms-conditions'))
        ->name('website.terms-conditions');

    Route::get('/terms-of-use', fn() => view('components.website.terms-of-use'))
        ->name('website.terms-of-use');

    Route::get('/demo', fn() => view('components.website.demo'))
        ->name('website.demo');
});
