<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GreetingApiController;

Route::get('/greetings', [GreetingApiController::class, 'index']);
Route::post('/greetings', [GreetingApiController::class, 'store']);
Route::put('/greetings/{greeting}', [GreetingApiController::class, 'update']);
Route::delete('/greetings/{greeting}', [GreetingApiController::class, 'destroy']);
