<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MovementController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware(['auth:sanctum'])->group(function () {

    Route::get('/user', fn (Request $request) => $request->user());
    
    Route::get('/products/low-stock', [ProductController::class, 'lowStock']);
    Route::get('/products/expiring', [ProductController::class, 'getExpiringProducts']);
    Route::resource('products', ProductController::class);

    Route::get('/user-logged-in', [UserController::class, 'getLoggedInUser']);
    Route::post('/logout', [UserController::class, 'logout']);

    Route::resource('suppliers', SupplierController::class)->except(['create', 'edit']);
    Route::resource('categories', CategoryController::class)->except(['create', 'edit']);

    Route::resource('movements', MovementController::class)->except(['create', 'edit']);
    Route::get('/stock-summary', [MovementController::class, 'getStockSummary']);
});

Route::middleware(['auth:sanctum', 'role:admin,manager'])->group(function () {
    Route::resource('users', UserController::class)->except(['create', 'edit']);
});


