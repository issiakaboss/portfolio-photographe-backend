<?php

use App\Http\Controllers\Api\ArtworkController;
use App\Http\Controllers\Api\TestimonialController;
use App\Http\Controllers\Api\SiteContentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/artworks', [ArtworkController::class, 'index']);
Route::get('/artworks/{id}', [ArtworkController::class, 'show']);
Route::get('/testimonials', [TestimonialController::class, 'index']);
Route::post('/testimonials', [TestimonialController::class, 'store'])->name('testimonials.store');
Route::get('/content/{section}', [SiteContentController::class, 'show']);
