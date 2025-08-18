<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::get('/user', function (Request $request) {
    return $request->user();
});

/**
 * Auth routes
 * */
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

/**
 * Product routes
 * */
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/products', [ProductController::class, 'index']);
Route::post('/products', [ProductController::class, 'store']);
Route::post('/products/{id}', [ProductController::class, 'update']);
Route::put('/products/{id}', [ProductController::class, 'updateImages']);
Route::delete('/products/{id}', [ProductController::class, 'destroy']);

/**
 * Category routes
 * */
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{id}', [CategoryController::class, 'show'])->name('categories.show');
Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

/**
 * User routes
 * */
Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::post('/users', [UserController::class, 'store']);
Route::put('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);

Route::middleware('auth:sanctum')->group(function(){
    Route::post('cart', [CartController::class,'store']);
    Route::get('cart', [CartController::class,'index']);
    Route::post('cart/checkout', [CartController::class,'checkout']);
    // vendor routes, admin routes — attach role middleware
});

/**
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
