<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/profile',          [ProfileController::class, 'getProfile']);
    Route::post('/upload/avatar',   [ProfileController::class, 'uploadAvatar']);
    Route::post('/signout',         [AuthController::class, 'signout']);
});


Route::post('/signin', [AuthController::class, 'signin']);
Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/forgot', [AuthController::class, 'forgot']);
Route::post('/verify', [AuthController::class, 'verify']);
