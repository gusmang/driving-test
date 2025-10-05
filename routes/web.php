<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('main');
});

Route::get('/{any}', function () {
    return view('main'); // atau blade utama kamu
})->where('any', '.*');
