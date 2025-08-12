<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/products', [ProductController::class, 'index']);
Route::post('/products', [ProductController::class, 'store']);

Route::middleware('auth:sanctum')->group(function(){
    Route::post('cart', [CartController::class,'store']);
    Route::get('cart', [CartController::class,'index']);
    Route::post('cart/checkout', [CartController::class,'checkout']);
    // vendor routes, admin routes — attach role middleware
});