<?php

use App\Http\Controllers\WebsiteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public website API
Route::prefix('website')->group(function () {
    Route::get('/stats',            [WebsiteController::class, 'stats']);
    Route::get('/schools',          [WebsiteController::class, 'schools']);
    Route::get('/testimonials',     [WebsiteController::class, 'testimonials']);
    Route::get('/privacy-policy',   [WebsiteController::class, 'privacyPolicy']);
    Route::get('/terms-conditions', [WebsiteController::class, 'termsConditions']);
    Route::get('/terms-of-use',     [WebsiteController::class, 'termsOfUse']);

    // Dynamic marketing pages (why-us, services, careers, become-executive, blogs, faqs)
    Route::get('/page/{slug}',      [WebsiteController::class, 'page']);

    // Anti-spam: 5 submissions per minute per IP on form endpoints.
    Route::middleware('throttle:5,1')->group(function () {
        Route::post('/contact', [WebsiteController::class, 'contact']);
        Route::post('/demo',    [WebsiteController::class, 'demo']);
    });
});

//v1 api
require __DIR__ . '/v1.php';
