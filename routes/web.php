<?php

use App\Http\Controllers\AeroDropController;
use App\Http\Controllers\Lawyer\CasesController;
use App\Http\Controllers\Lawyer\AppointmentController;
use App\Http\Controllers\Lawyer\AuthController;
use App\Http\Controllers\Lawyer\CalendarController;
use App\Http\Controllers\Lawyer\CaseAttachmentController;
use App\Http\Controllers\Lawyer\ClientAttachmentController;
use App\Http\Controllers\Lawyer\ClientController;
use App\Http\Controllers\Lawyer\NotificationController;
use App\Http\Controllers\Lawyer\ProfileController;
use App\Http\Controllers\Lawyer\ResetPasswordController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

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
    
    Route::get('/profile',                  [ProfileController::class, 'profile'])->name('lawyer.profile');
    Route::put('/profile',                  [ProfileController::class, 'update']);
    Route::post('/profile/avatar',          [ProfileController::class, 'uploadAvatar'])->name('lawyer.avatar.upload');
    Route::delete('/profile/avatar',        [ProfileController::class, 'deleteAvatar'])->name('lawyer.avatar.delete');
    Route::put('/profile/reset-password',   [ResetPasswordController::class, 'update'])->name('lawyer.reset-password');
    Route::get('/signout',                  [AuthController::class, 'signout'])->name('lawyer.signout');


    // Management routes
    Route::resource('/manage/appointments', AppointmentController::class, [
        'names' => [
            'index'   => 'lawyer.appointment.index',
            'create'  => 'lawyer.appointment.create',
            'store'   => 'lawyer.appointment.store',
            'show'    => 'lawyer.appointment.show',
            'edit'    => 'lawyer.appointment.edit',
            'update'  => 'lawyer.appointment.update',
            'destroy' => 'lawyer.appointment.destroy',
        ]
    ]);
    
    Route::resource('/manage/cases', CasesController::class, [
        'names' => [
            'index'   => 'lawyer.cases.index',
            'create'  => 'lawyer.cases.create',
            'store'   => 'lawyer.cases.store',
            'show'    => 'lawyer.cases.show',
            'edit'    => 'lawyer.cases.edit',
            'update'  => 'lawyer.cases.update',
            'destroy' => 'lawyer.cases.destroy',
        ]
    ]);
    Route::delete('/manage/cases/{case}/attachment/{attachment}', [CaseAttachmentController::class, 'destroy'])->name('lawyer.cases.attachments.destroy');
    
    Route::resource('/manage/client', ClientController::class, [
        'names' => [
            'index'   => 'lawyer.client.index',
            'create'  => 'lawyer.client.create',
            'store'   => 'lawyer.client.store',
            'show'    => 'lawyer.client.show',
            'edit'    => 'lawyer.client.edit',
            'update'  => 'lawyer.client.update',
            'destroy' => 'lawyer.client.destroy',
        ]
    ]);
    Route::delete('/manage/client/{client}/attachment/{attachment}', [ClientAttachmentController::class, 'destroy'])->name('lawyer.client.attachments.destroy');

    // Event calendar
    Route::get('/calendar', [CalendarController::class, 'index'])->name('lawyer.calendar.index');
    Route::get('/calendar/events', [CalendarController::class, 'events'])->name('lawyer.calendar.events');

    // notifications
    Route::get('/notification',                     [NotificationController::class, 'getNotifications']);
    Route::patch('/notification/{notification}',    [NotificationController::class, 'markRead']);

    // aerodrop
    Route::post('/upload', [AeroDropController::class, 'upload']);

    Route::view('/foo', 'welcome');
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





