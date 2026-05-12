<?php

use App\Http\Controllers\TestController;
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
    Route::post('/contact',         [WebsiteController::class, 'contact']);
    Route::post('/demo',            [WebsiteController::class, 'demo']);
});

//v1 api 
require __DIR__ . '/v1.php';

//Test Api 
Route::post('/test', [TestController::class, 'index']);

//Save Data 
Route::post('/save-data', [TestController::class, 'saveData']);
