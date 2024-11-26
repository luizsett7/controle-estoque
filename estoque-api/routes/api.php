<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);    

Route::middleware(['auth:sanctum', 'role:admin,manager'])->group(function () {
    Route::resource('users', UserController::class); // CRUD for users
});

Route::get('/products/low-stock', [ProductController::class, 'lowStock']);
Route::get('/products/expiring', [ProductController::class, 'getExpiringProducts']);

