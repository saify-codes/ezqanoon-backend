<?php

use App\Http\Controllers\AeroDropController;
use App\Http\Controllers\Lawyer\CasesController;
use App\Http\Controllers\Lawyer\AppointmentController;
use App\Http\Controllers\Lawyer\AuthController;
use App\Http\Controllers\Lawyer\CalendarController;
use App\Http\Controllers\Lawyer\CaseAttachmentController;
use App\Http\Controllers\Lawyer\ClientAttachmentController;
use App\Http\Controllers\Lawyer\ClientController;
use App\Http\Controllers\Lawyer\DashboardController;
use App\Http\Controllers\Lawyer\InvoiceController;
use App\Http\Controllers\Lawyer\NotificationController;
use App\Http\Controllers\Lawyer\OTPController;
use App\Http\Controllers\Lawyer\ProfileController;
use App\Http\Controllers\Lawyer\ResetPasswordController;
use App\Http\Controllers\Lawyer\SubscriptionController;
use App\Http\Controllers\Lawyer\TaskController;
use App\Http\Controllers\Lawyer\TeamController;
use App\Http\Controllers\Admin\ZoomController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

/*
|--------------------------------------------------------------------------
| 🚀 ADMIN ROUTES 🚀
|--------------------------------------------------------------------------
| These routes are accessible only by Admin users.
| Ensure that the 'admin' middleware is applied to protect these routes.
|--------------------------------------------------------------------------
*/
Route::group([], function(){
    Route::get('/admin/dashboard', fn() => "Welcome, Admin!");
    Route::get('/admin/users', fn() => "Manage Users");
    Route::get('/integrations/zoom/oauth/authorize', [ZoomController::class, 'authenticate'])->name('integration.zoom.authorize');
    Route::get('/integrations/zoom/oauth/callback', [ZoomController::class, 'handleOAuthCallback'])->name('integration.zoom.callback');
    Route::get('/integrations/zoom/success', [ZoomController::class, 'success'])->name('integration.zoom.success');
    Route::get('/integrations/zoom/error', [ZoomController::class, 'error'])->name('integration.zoom.error');
});
/*
|--------------------------------------------------------------------------
| ⚖️ LAWYER ROUTES ⚖️
|--------------------------------------------------------------------------
| These routes are designated for Lawyers.
| 'lawyer.auth' middleware ensures only authenticated lawyers can access.
|--------------------------------------------------------------------------
*/


Route::group(['middleware' => 'lawyer.auth'], function () {

    Route::group(['middleware' => 'lawyer.has_subscription', 'lawyer.verified'], function () {

        Route::get('/', [DashboardController::class, 'dashboard'])->name('lawyer.dashboard');

        Route::get('/profile',                  [ProfileController::class, 'profile'])->name('lawyer.profile');
        Route::put('/profile',                  [ProfileController::class, 'update']);
        Route::post('/profile/avatar',          [ProfileController::class, 'uploadAvatar'])->name('lawyer.avatar.upload');
        Route::delete('/profile/avatar',        [ProfileController::class, 'deleteAvatar'])->name('lawyer.avatar.delete');
        Route::put('/profile/reset-password',   [ResetPasswordController::class, 'update'])->name('lawyer.reset-password');

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
                'index'     => 'lawyer.cases.index',
                'create'    => 'lawyer.cases.create',
                'store'     => 'lawyer.cases.store',
                'show'      => 'lawyer.cases.show',
                'edit'      => 'lawyer.cases.edit',
                'update'    => 'lawyer.cases.update',
                'destroy'   => 'lawyer.cases.destroy',
                ]
        ]);
        Route::patch('/cases/{caseId}/hearing/{hearingId}', [CasesController::class, 'changeHearingDate'])->name('lawyer.cases.hearing');

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
        Route::resource('/manage/team', TeamController::class, [
            'names' => [
                'index'             => 'lawyer.team.index',
                'create'            => 'lawyer.team.create',
                'store'             => 'lawyer.team.store',
                'show'              => 'lawyer.team.show',
                'edit'              => 'lawyer.team.edit',
                'update'            => 'lawyer.team.update',
                'destroy'           => 'lawyer.team.destroy',
            ]
        ]);
        Route::resource('/manage/task', TaskController::class, [
            'names' => [
                'index'             => 'lawyer.task.index',
                'create'            => 'lawyer.task.create',
                'store'             => 'lawyer.task.store',
                'show'              => 'lawyer.task.show',
                'edit'              => 'lawyer.task.edit',
                'update'            => 'lawyer.task.update',
                'destroy'           => 'lawyer.task.destroy',
            ]
        ]);
        Route::put('/manage/team/{user}/change-password', [TeamController::class, 'changePassword'])->name('lawyer.team.change-password');
        Route::delete('/manage/cases/{case}/attachment/{attachment}', [CaseAttachmentController::class, 'destroy'])->name('lawyer.cases.attachments.destroy');
        Route::delete('/manage/client/{client}/attachment/{attachment}', [ClientAttachmentController::class, 'destroy'])->name('lawyer.client.attachments.destroy');

        // Event calendar
        Route::get('/calendar/events', [CalendarController::class, 'events'])->name('lawyer.calendar.events');
        Route::resource('/calendar', CalendarController::class, [
            'names' => [
                'index'             => 'lawyer.calendar.index',
                'create'            => 'lawyer.calendar.create',
                'store'             => 'lawyer.calendar.store',
                'show'              => 'lawyer.calendar.show',
                'edit'              => 'lawyer.calendar.edit',
                'update'            => 'lawyer.calendar.update',
                'destroy'           => 'lawyer.calendar.destroy',
            ]
        ]);

        // Billing & Invoice
        Route::resource('/invoice', InvoiceController::class, [
            'names' => [
                'index'             => 'lawyer.invoice.index',
                'create'            => 'lawyer.invoice.create',
                'store'             => 'lawyer.invoice.store',
                'show'              => 'lawyer.invoice.show',
                'edit'              => 'lawyer.invoice.edit',
                'update'            => 'lawyer.invoice.update',
                'destroy'           => 'lawyer.invoice.destroy',
            ]
        ]);
    });

    // notifications
    Route::get('/notification',                     [NotificationController::class, 'getNotifications']);
    Route::patch('/notification/{notification}',    [NotificationController::class, 'markRead']);

    Route::get('/signout', [AuthController::class, 'signout'])->name('lawyer.signout');

    // Only allow access to subscription page if user doesn't have an active subscription
    Route::get('/plane/select', [SubscriptionController::class, 'index'])->middleware('lawyer.no_subscription')->name('lawyer.subscription');
    Route::get('/plane/select/{subscription}', [SubscriptionController::class, 'select'])->middleware('lawyer.no_subscription')->name('lawyer.subscription.select');

    // aerodrop
    Route::post('/upload', [AeroDropController::class, 'upload']);
});

Route::group(['middleware' => 'lawyer.guest'], function () {

    Route::view('/signin', 'lawyer.signin')->name('lawyer.signin');
    Route::post('/signin', [AuthController::class, 'signin']);

    Route::view('/signup', 'lawyer.signup')->name('lawyer.signup');
    Route::post('/signup', [AuthController::class, 'signup']);

    Route::view('/forgot', 'lawyer.forgot')->name('lawyer.forgot');
    Route::post('/forgot', [AuthController::class, 'forgot']);

    Route::view('/reset/{token}', 'lawyer.reset')->name('lawyer.reset');
    Route::post('/reset', [AuthController::class, 'reset'])->name('lawyer.reset');

    Route::get('/verify/{token}', [AuthController::class, 'verify']);

    Route::get('/verification/resend', [AuthController::class, 'sendVerificationLink'])->name('lawyer.verification.resend');

    Route::post('/otp/send', [OTPController::class, 'sendOTP'])->name('lawyer.otp.send');

    Route::post('/otp/verify', [OTPController::class, 'verifyOTP'])->name('lawyer.otp.verify');
});

Route::any('/foo', function (\Illuminate\Http\Request $request) {
    
    $v = Validator::make($request->all(),[
        'phone' => 'required|phone'
    ]);

    if ($v->fails()) {
        dd($v->errors(), $request->phone);
    }

    dd($v->validated());
    
});