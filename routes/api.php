<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function(){
    Route::post('register', 'register');
    Route::post('login', 'login');
});

Route::post('/logout', [AuthController::class])->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->get('/notifications', [AuthController::class, 'getNotifications']);
