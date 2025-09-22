<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\ReportController;

// Caso deseje conferir só os produtos, descomente e use o endpoint
// Route::get('/product', [ProductController::class, 'index']);

Route::prefix('inventory')->group(function () {
    Route::post('/', [InventoryController::class, 'store']); 
    Route::get('/', [InventoryController::class, 'index']);   
});

Route::prefix('sales')->group(function () {
    Route::post('/', [SaleController::class, 'store']); 
    Route::get('/{id}', [SaleController::class, 'show']); 
});

    Route::get('reports/sales', [ReportController::class, 'sales']);
