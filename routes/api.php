<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\LawyerController;
use App\Http\Controllers\Api\OTPController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/profile',          [ProfileController::class, 'getProfile']);
    Route::put('/profile',          [ProfileController::class, 'updateProfile']);
    Route::put('/password',         [ProfileController::class, 'updatePassword']);
    Route::post('/upload/avatar',   [ProfileController::class, 'uploadAvatar']);
    Route::get('/appointment',      [AppointmentController::class, 'getAppointments']);
    Route::post('/appointment',     [AppointmentController::class, 'makeAppointment']);
    Route::post('/signout',         [AuthController::class, 'signout']);
});


Route::post('/signin', [AuthController::class, 'signin']);
Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/forgot', [AuthController::class, 'forgot']);
Route::post('/verify', [AuthController::class, 'verify']);
Route::post('/reset',  [AuthController::class, 'reset']);
Route::post('/resend',  [AuthController::class, 'sendVerificationLink']);
Route::post('/otp/send', [OTPController::class, 'sendOTP'])->name('lawyer.otp.send');
Route::post('/otp/verify', [OTPController::class, 'verifyOTP'])->name('lawyer.otp.verify');

/*-------------------------------------------------------
|                      LAWYERS API                      |
|-------------------------------------------------------|
|  This section contains all routes related to lawyers |
|-------------------------------------------------------*/

Route::get('/lawyer', [LawyerController::class, 'getLawyers']);
Route::get('/lawyer/{id}', [LawyerController::class, 'getLawyer']);
Route::get('/lawyer/{id}/reviews', [LawyerController::class, 'getReviews']);




Route::any('/foo', function(){
    return Session::id();
})->middleware('web');