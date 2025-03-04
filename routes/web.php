<?php

use App\Http\Controllers\Lawyer\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| 🚀 ADMIN ROUTES 🚀
|--------------------------------------------------------------------------
| These routes are accessible only by Admin users.
| Ensure that the 'admin' middleware is applied to protect these routes.
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => 'admin.auth'], function(){
    Route::get('/admin/dashboard', fn() => "Welcome, Admin!");
    Route::get('/admin/users', fn() => "Manage Users");
});

/*
|--------------------------------------------------------------------------
| ⚖️ LAWYER ROUTES ⚖️
|--------------------------------------------------------------------------
| These routes are designated for Lawyers.
| 'lawyer.auth' middleware ensures only authenticated lawyers can access.
|--------------------------------------------------------------------------
*/


Route::group(['middleware' => 'lawyer.auth'], function(){
    Route::get('/lawyer/dashboard', fn() => "Welcome, Lawyer!");
    Route::get('/lawyer/cases', fn() => "Manage Cases");
});


Route::view('/signin', 'lawyer.signin')->name('lawyer.signin');
Route::post('/signin', [AuthController::class, 'signin']);

Route::view('/signup', 'lawyer.signup')->name('lawyer.signup');
Route::post('/signup', [AuthController::class, 'signup']);

Route::get('/verification/resend', [AuthController::class, 'sendVerificationLink'])->name('lawyer.verification.resend');


Route::view('/', 'welcome');
