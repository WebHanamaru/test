<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// 文字列を返すルート
Route::get('/hello', function () {
    return 'Hello, Laravel!';
});

// viewを返すルート
Route::get('/about', function () {
    return view('about');
});

// 挨拶ページ
Route::get('/greeting', [App\Http\Controllers\GreetingController::class, 'index'])->name('greeting.index');
Route::post('/greeting', [App\Http\Controllers\GreetingController::class, 'store'])->name('greeting.store');
Route::delete('/greeting/{greeting}', [App\Http\Controllers\GreetingController::class, 'destroy'])->name('greeting.destroy');

// API デモページ
Route::get('/api-demo', function () {
    return view('api-demo');
});
