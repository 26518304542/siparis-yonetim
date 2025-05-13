<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\OrderController;

use App\Models\Customer;
use App\Models\Product;

Route::apiResource('orders', OrderController::class);


Route::get('/customers', function () {
    return Customer::all();
});

Route::get('/products', function () {
    return Product::all();
});
