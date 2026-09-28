<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PemasukanController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\AuthController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/profile/photo', [AuthController::class, 'uploadPhoto']);

// ==================== PRODUK ====================

Route::get('/products', [ProductController::class, 'index']);
Route::post('/products', [ProductController::class, 'store']);
Route::put('/products/{product}', [ProductController::class, 'update']);
Route::delete('/products/{product}', [ProductController::class, 'destroy']);

Route::post('/checkout', [CheckoutController::class, 'store']);
Route::get('/orders', [CheckoutController::class, 'index']);

Route::get('/expenses', [ExpenseController::class, 'index']);
Route::post('/expenses', [ExpenseController::class, 'store']);
Route::put('/expenses/{expense}', [ExpenseController::class, 'update']);
Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy']);


// ==================== PEMASUKAN ====================

Route::get('/pemasukan', [PemasukanController::class, 'index']);
Route::post('/pemasukan', [PemasukanController::class, 'store']);

Route::get('/pemasukan/{id}', [PemasukanController::class, 'show']);
Route::put('/pemasukan/{id}', [PemasukanController::class, 'update']);
Route::delete('/pemasukan/{id}', [PemasukanController::class, 'destroy']);