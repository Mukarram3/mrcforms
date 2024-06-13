<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::get('/get-order-data', [\App\Http\Controllers\Api\OrderController::class, 'getOrderData']);

Route::get('/get-order/details/{id}', [\App\Http\Controllers\Api\OrderController::class, 'orderDetails']);

Route::post('/update-delivery-status', [\App\Http\Controllers\Api\OrderController::class, 'updateDeliveryStatus']);


Route::post('/update-stock', [\App\Http\Controllers\Api\UpdateStockController::class, 'updateStock']);

Route::post('/product-update', [\App\Http\Controllers\Api\UpdateStockController::class, 'productUpdate']);


Route::post('/add-product', [\App\Http\Controllers\Api\ProductController::class, 'storeProduct']);

Route::post('/update-bulk-record', [\App\Http\Controllers\Api\UpdateStockController::class, 'stockUploadinBulk']);





