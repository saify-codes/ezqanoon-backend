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
    Route::view('/', 'lawyer.dashboard')->name('lawyer.dashboard');
    Route::get('/signout', [AuthController::class, 'signout'])->name('lawyer.signout');
});

Route::group(['middleware' => 'lawyer.guest'], function(){

    Route::view('/signin', 'lawyer.signin')->name('lawyer.signin');
    Route::post('/signin', [AuthController::class, 'signin']);
    
    Route::view('/signup', 'lawyer.signup')->name('lawyer.signup');
    Route::post('/signup', [AuthController::class, 'signup']);
    
    Route::view('/forgot', 'lawyer.forgot')->name('lawyer.forgot');
    Route::post('/forgot', [AuthController::class, 'forgot']);
    
    Route::view('/reset/{token}','lawyer.reset');
    Route::post('/reset', [AuthController::class, 'reset'])->name('lawyer.reset');
    
    Route::get('/verify/{token}', [AuthController::class, 'verify']);
    
    Route::get('/verification/resend', [AuthController::class, 'sendVerificationLink'])->name('lawyer.verification.resend');

});    




