<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\AttributeController;
use App\Http\Controllers\Api\AttributeOptionController;
use App\Http\Controllers\Api\VariantController;
use App\Http\Controllers\Api\ProductImageController;
use App\Http\Controllers\Api\ReviewController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/products', [ProductController::class, 'index']);
Route::post('/products', [ProductController::class, 'store']);
Route::post('/products/{id}', [ProductController::class, 'update']);
Route::delete('/products/{id}', [ProductController::class, 'destroy']);

Route::middleware('auth:sanctum')->group(function(){
    Route::post('cart', [CartController::class,'store']);
    Route::get('cart', [CartController::class,'index']);
    Route::post('cart/checkout', [CartController::class,'checkout']);
    // vendor routes, admin routes — attach role middleware
});

/*
 * Variant routes
 */
Route::put('/variants/{id}', [VariantController::class, 'update']);
Route::delete('/variants/{id}', [VariantController::class, 'destroy']);


/**
 * Test route to check if API is working
*/
Route::get('/test', function () {
    return response()->json(['message' => 'API is working!']);
});
