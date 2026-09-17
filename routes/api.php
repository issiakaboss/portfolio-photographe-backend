<?php

use App\Http\Controllers\Api\ArtworkController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\SiteContentController;
use App\Http\Controllers\Api\TestimonialController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/artworks', [ArtworkController::class, 'index']);
Route::get('/artworks/{id}', [ArtworkController::class, 'show']);
Route::get('/testimonials', [TestimonialController::class, 'index']);
Route::post('/testimonials', [TestimonialController::class, 'store'])->name('testimonials.store');
Route::post('/orders/capture', [OrderController::class, 'capture'])->name('orders.capture');
Route::get('/content/{section}', [SiteContentController::class, 'show']);
