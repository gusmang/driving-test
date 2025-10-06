<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;

Route::get('/', function () {
    return view('main');
});


Route::get('/docs/api', function () {
    return redirect()->route('scramble.docs.ui');
})->middleware('basic.auth');


// // Route untuk logout (clear auth)
Route::get('/docs/logout', function () {
    session()->forget('basic_auth_authenticated');
    session()->forget('basic_auth_user');

    // 2. Trik URL: Redirect ke URL dengan kredensial palsu (fail:logout)
    //    Ini memaksa browser untuk menimpa kredensial lama dan memunculkan pop-up baru.
    $host = request()->getHost();
    $port = request()->getPort() ? ':' . request()->getPort() : '';

    // Redirect ke: http://fail:logout@localhost:8004/docs/api
    return redirect()->to('http://fail:logout@' . $host . $port . '/docs/api');
})->name('docs.logout');

Route::get('/{any}', function () {
    return view('main'); // atau blade utama kamu
})->where('any', '.*');
