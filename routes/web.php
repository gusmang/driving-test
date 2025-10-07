<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;

Route::get('/', function () {
    return view('main');
});


Route::get('/docs/api', function () {
    return redirect()->route('scramble.docs.ui');
})->middleware('basic.auth');

Route::get('/docs/logout', function () {
    session()->forget('basic_auth_authenticated');
    session()->forget('basic_auth_user');

    $host = request()->getHost();
    $port = request()->getPort() ? ':' . request()->getPort() : '';
    return redirect()->to('http://fail:logout@' . $host . $port . '/docs/api');
})->name('docs.logout');

Route::get('/{any}', function () {
    return view('main');
})->where('any', '.*');
