<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PemasukanController;

// ==================== PRODUK ====================

Route::get('/products', [ProductController::class, 'index']);
Route::post('/products', [ProductController::class, 'store']);


// ==================== PEMASUKAN ====================

Route::get('/pemasukan', [PemasukanController::class, 'index']);
Route::post('/pemasukan', [PemasukanController::class, 'store']);

Route::get('/pemasukan/{id}', [PemasukanController::class, 'show']);
Route::put('/pemasukan/{id}', [PemasukanController::class, 'update']);
Route::delete('/pemasukan/{id}', [PemasukanController::class, 'destroy']);