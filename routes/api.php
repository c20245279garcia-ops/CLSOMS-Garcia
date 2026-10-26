<?php

use App\Http\Controllers\LaundryOrderController;
use App\Http\Controllers\DeliveryController;
use Illuminate\Support\Facades\Route;

Route::apiResource('laundry-orders', LaundryOrderController::class)
     ->only(['index', 'create', 'store', 'update']);

Route::apiResource('deliveries', DeliveryController::class)
     ->only(['index', 'create', 'store', 'update']);