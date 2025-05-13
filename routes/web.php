<?php

use Illuminate\Support\Facades\Route;

Route::get('/siparisler', function () {
    return view('orders');
});

Route::get('/', fn() => 'OK');
