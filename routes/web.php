<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AeroDropController;

use App\Http\Controllers\Admin\ZoomController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\LawyerController as AdminLawyerController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\ResetPasswordController as AdminResetPasswordController;

use App\Http\Controllers\Lawyer\CasesController;
use App\Http\Controllers\Lawyer\AppointmentController;
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
use App\Http\Controllers\Lawyer\SettingController;

use App\Http\Controllers\Firm\DashboardController as FirmDashboardController;
use App\Http\Controllers\Firm\InvoiceController as FirmInvoiceController;
use App\Http\Controllers\Firm\NotificationController as FirmNotificationController;
use App\Http\Controllers\Firm\ProfileController as FirmProfileController;
use App\Http\Controllers\Firm\ResetPasswordController as FirmResetPasswordController;
use App\Http\Controllers\Firm\SettingController as FirmSettingController;
use App\Http\Controllers\Firm\SubscriptionController as FirmSubscriptionController;
use App\Http\Controllers\Firm\TaskController as FirmTaskController;
use App\Http\Controllers\Firm\TeamController as FirmTeamController;
use App\Http\Controllers\Firm\AppointmentController as FirmAppointmentController;
use App\Http\Controllers\Firm\CalendarController as FirmCalendarController;
use App\Http\Controllers\Firm\CaseAttachmentController as FirmCaseAttachmentController;
use App\Http\Controllers\Firm\CasesController as FirmCasesController;
use App\Http\Controllers\Firm\ClientController as FirmClientController;
use App\Http\Controllers\Team\AppointmentController as TeamAppointmentController;
use App\Http\Controllers\Team\CalendarController as TeamCalendarController;
use App\Http\Controllers\Team\CaseAttachmentController as TeamCaseAttachmentController;
use App\Http\Controllers\Team\CasesController as TeamCasesController;
use App\Http\Controllers\Team\ClientAttachmentController as TeamClientAttachmentController;
use App\Http\Controllers\Team\ClientController as TeamClientController;
use App\Http\Controllers\Team\DashboardController as TeamDashboardController;
use App\Http\Controllers\Team\InvoiceController as TeamInvoiceController;
use App\Http\Controllers\Team\NotificationController as TeamNotificationController;
use App\Http\Controllers\Team\ProfileController as TeamProfileController;
use App\Http\Controllers\Team\ResetPasswordController as TeamResetPasswordController;
use App\Http\Controllers\Team\TaskController as TeamTaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| 🛡️ ADMIN ROUTES 🛡️
|--------------------------------------------------------------------------
| These routes are accessible only by Admin users.
| Ensure that the 'admin' middleware is applied to protect these routes.
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => 'admin.auth', 'prefix' => 'admin'], function(){
    Route::get('/',                         [AdminDashboardController::class, 'dashboard'])->name('admin.dashboard');

    Route::get('/profile',                  [AdminProfileController::class, 'profile'])->name('admin.profile');
    Route::put('/profile',                  [AdminProfileController::class, 'update']);
    Route::post('/profile/file/{type}',     [AdminProfileController::class,'store'  ])->name('admin.file.upload');
    Route::delete('/profile/file/{type}',   [AdminProfileController::class,'destroy'])->name('admin.file.delete');
    Route::put('/profile/reset-password',   [AdminResetPasswordController::class, 'update'])->name('admin.reset-password');
    
    // Management routes
    Route::resource('/manage/lawyer', AdminLawyerController::class, [
        'names' => [
            'index'             => 'admin.lawyer.index',
            'create'            => 'admin.lawyer.create',
            'store'             => 'admin.lawyer.store',
            'show'              => 'admin.lawyer.show',
            'edit'              => 'admin.lawyer.edit',
            'update'            => 'admin.lawyer.update',
            'destroy'           => 'admin.lawyer.destroy',
        ]
    ]);

    // Integrations
    Route::get('/signout', [AdminAuthController::class, 'signout'])->name('admin.signout');
    
});

Route::get('/integrations/zoom/oauth/authorize', [ZoomController::class, 'authenticate'])->name('integration.zoom.authorize');
Route::get('/integrations/zoom/oauth/callback', [ZoomController::class, 'handleOAuthCallback'])->name('integration.zoom.callback');
Route::get('/integrations/zoom/success', [ZoomController::class, 'success'])->name('integration.zoom.success');
Route::get('/integrations/zoom/error', [ZoomController::class, 'error'])->name('integration.zoom.error');

Route::group(['middleware' => 'admin.guest', 'prefix' => 'admin'], function () {

    Route::view('/signin', 'admin.signin')->name('admin.signin');
    Route::post('/signin', [AdminAuthController::class, 'signin']);

});

/*
|--------------------------------------------------------------------------
| 💼 Firm ROUTES 💼
|--------------------------------------------------------------------------
| These routes are designated for firms.
| 'firm.auth' middleware ensures only authenticated firms can access.
|--------------------------------------------------------------------------
*/


Route::group(['middleware' => 'firm.auth', 'prefix' => 'firm'], function () {

    Route::group(['middleware' => 'firm.subscribed', 'firm.verified'], function () {

        Route::get('/', [FirmDashboardController::class, 'dashboard'])->name('firm.dashboard');

        Route::get('/profile',                  [FirmProfileController::class, 'profile'])->name('firm.profile');
        Route::put('/profile',                  [FirmProfileController::class, 'update']);
        Route::post('/profilefile/{type}',      [FirmProfileController::class,'store'  ])->name('firm.file.upload');
        Route::delete('/profilefile/{type}',    [FirmProfileController::class,'destroy'])->name('firm.file.delete');
        Route::put('/profile/reset-password',   [FirmResetPasswordController::class, 'update'])->name('firm.reset-password');


        // Management routes
        Route::resource('/manage/team', FirmTeamController::class, [
            'names' => [
                'index'             => 'firm.team.index',
                'create'            => 'firm.team.create',
                'store'             => 'firm.team.store',
                'show'              => 'firm.team.show',
                'edit'              => 'firm.team.edit',
                'update'            => 'firm.team.update',
                'destroy'           => 'firm.team.destroy',
            ]
        ]);
        Route::put('/manage/team/{user}/change-password', [FirmTeamController::class, 'changePassword'])->name('firm.team.change-password');

        Route::resource('/manage/client', FirmClientController::class, [
            'names' => [
                'index'   => 'firm.client.index',
                'create'  => 'firm.client.create',
                'store'   => 'firm.client.store',
                'show'    => 'firm.client.show',
                'edit'    => 'firm.client.edit',
                'update'  => 'firm.client.update',
                'destroy' => 'firm.client.destroy',
            ]
        ]);
        Route::delete('/manage/client/{client}/attachment/{attachment}', [ClientAttachmentController::class, 'destroy'])->name('firm.client.attachments.destroy');

        Route::resource('/manage/cases', FirmCasesController::class, [
            'names' => [
                'index'     => 'firm.cases.index',
                'create'    => 'firm.cases.create',
                'store'     => 'firm.cases.store',
                'show'      => 'firm.cases.show',
                'edit'      => 'firm.cases.edit',
                'update'    => 'firm.cases.update',
                'destroy'   => 'firm.cases.destroy',
                ]
        ]);
        Route::delete('/manage/cases/{case}/attachment/{attachment}', [FirmCaseAttachmentController::class, 'destroy'])->name('firm.cases.attachments.destroy');
        Route::patch('/cases/{caseId}/hearing/{hearingId}', [FirmCasesController::class, 'changeHearingDate'])->name('firm.cases.hearing');


        Route::resource('/manage/appointments', FirmAppointmentController::class, [
            'names' => [
                'index'   => 'firm.appointment.index',
                'create'  => 'firm.appointment.create',
                'store'   => 'firm.appointment.store',
                'show'    => 'firm.appointment.show',
                'edit'    => 'firm.appointment.edit',
                'update'  => 'firm.appointment.update',
                'destroy' => 'firm.appointment.destroy',
            ]
        ]);

        Route::resource('/manage/task', FirmTaskController::class, [
            'names' => [
                'index'             => 'firm.task.index',
                'create'            => 'firm.task.create',
                'store'             => 'firm.task.store',
                'show'              => 'firm.task.show',
                'edit'              => 'firm.task.edit',
                'update'            => 'firm.task.update',
                'destroy'           => 'firm.task.destroy',
            ]
        ]);
       
        // // Event calendar
        Route::get('/calendar/events', [FirmCalendarController::class, 'events'])->name('firm.calendar.events');
        Route::resource('/calendar', FirmCalendarController::class, [
            'names' => [
                'index'             => 'firm.calendar.index',
                'create'            => 'firm.calendar.create',
                'store'             => 'firm.calendar.store',
                'show'              => 'firm.calendar.show',
                'edit'              => 'firm.calendar.edit',
                'update'            => 'firm.calendar.update',
                'destroy'           => 'firm.calendar.destroy',
            ]
        ]);

        // // Billing & Invoice
        Route::resource('/invoice', FirmInvoiceController::class, [
            'names' => [
                'index'             => 'firm.invoice.index',
                'create'            => 'firm.invoice.create',
                'store'             => 'firm.invoice.store',
                'show'              => 'firm.invoice.show',
                'edit'              => 'firm.invoice.edit',
                'update'            => 'firm.invoice.update',
                'destroy'           => 'firm.invoice.destroy',
            ]
        ]);

        // // Settings
        Route::resource('/settings', FirmSettingController::class, [
            'names' => [
                'index'             => 'firm.settings.index',
                'create'            => 'firm.settings.create',
                'store'             => 'firm.settings.store',
                'show'              => 'firm.settings.show',
                'edit'              => 'firm.settings.edit',
                'update'            => 'firm.settings.update',
                'destroy'           => 'firm.settings.destroy',
            ]
        ]);
    });

    // notifications
    Route::get('/notification',                     [FirmNotificationController::class, 'getNotifications']);
    Route::patch('/notification/{notification}',    [FirmNotificationController::class, 'markRead']);

    Route::get('/signout', [AuthController::class, 'signout'])->name('firm.signout');

    // Only allow access to subscription page if user doesn't have an active subscription
    Route::get('/plane/select', [FirmSubscriptionController::class, 'index'])->middleware('firm.unsubscribed')->name('firm.subscription');
    Route::post('/plane/select/{subscription}', [FirmSubscriptionController::class, 'select'])->middleware('firm.unsubscribed')->name('firm.subscription.select');

    // aerodrop
    Route::post('/upload', [AeroDropController::class, 'upload']);
});

/*
|--------------------------------------------------------------------------
| 🎓 LAWYER ROUTES 🎓
|--------------------------------------------------------------------------
| These routes are designated for Lawyers.
| 'lawyer.auth' middleware ensures only authenticated lawyers can access.
|--------------------------------------------------------------------------
*/


Route::group(['middleware' => 'lawyer.auth'], function () {

    Route::group(['middleware' => 'lawyer.subscribed', 'lawyer.verified'], function () {

        Route::get('/', [DashboardController::class, 'dashboard'])->name('lawyer.dashboard');

        Route::get('/profile',                  [ProfileController::class, 'profile'])->name('lawyer.profile');
        Route::put('/profile',                  [ProfileController::class, 'update']);
        Route::post('/profile/file/{type}',     [ProfileController::class,'store'  ])->name('lawyer.file.upload');
        Route::delete('/profile/file/{type}',   [ProfileController::class,'destroy'])->name('lawyer.file.delete');
        Route::put('/profile/reset-password',   [ResetPasswordController::class, 'update'])->name('lawyer.reset-password');



        // Management routes
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
        Route::put('/manage/team/{user}/change-password', [TeamController::class, 'changePassword'])->name('lawyer.team.change-password');

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
        Route::delete('/manage/cases/{case}/attachment/{attachment}', [CaseAttachmentController::class, 'destroy'])->name('lawyer.cases.attachments.destroy');

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

        // Settings
        Route::resource('/settings', SettingController::class, [
            'names' => [
                'index'             => 'lawyer.settings.index',
                'create'            => 'lawyer.settings.create',
                'store'             => 'lawyer.settings.store',
                'show'              => 'lawyer.settings.show',
                'edit'              => 'lawyer.settings.edit',
                'update'            => 'lawyer.settings.update',
                'destroy'           => 'lawyer.settings.destroy',
            ]
        ]);
    });

    // notifications
    Route::get('/notification',                     [NotificationController::class, 'getNotifications']);
    Route::patch('/notification/{notification}',    [NotificationController::class, 'markRead']);

    Route::get('/signout', [AuthController::class, 'signout'])->name('lawyer.signout');

    // Only allow access to subscription page if user doesn't have an active subscription
    Route::get('/plane/select', [SubscriptionController::class, 'index'])->middleware('lawyer.unsubscribed')->name('lawyer.subscription');
    Route::post('/plane/select/{subscription}', [SubscriptionController::class, 'select'])->middleware('lawyer.unsubscribed')->name('lawyer.subscription.select');

    // aerodrop
    Route::post('/upload', [AeroDropController::class, 'upload']);
});

/*
|--------------------------------------------------------------------------
| 👥 TEAM ROUTES 👥
|--------------------------------------------------------------------------
| These routes are designated for firms.
| 'team.auth' middleware ensures only authenticated firms can access.
|--------------------------------------------------------------------------
*/


Route::group(['middleware' => 'team.auth', 'prefix' => 'team'], function () {

    Route::group(['middleware' => 'team.owner_subscribed'], function () {

        Route::get('/', [TeamDashboardController::class, 'dashboard'])->name('team.dashboard');

        Route::get('/profile',                  [TeamProfileController::class, 'profile'])->name('team.profile');
        Route::put('/profile',                  [TeamProfileController::class, 'update']);
        Route::put('/profile/reset-password',   [TeamResetPasswordController::class, 'update'])->name('team.reset-password');

        Route::post  ('file/{type}', [TeamProfileController::class,'store'  ])->name('team.file.upload');
        Route::delete('file/{type}', [TeamProfileController::class,'destroy'])->name('team.file.delete');

        // Management routes
        Route::resource('/manage/task', TeamTaskController::class, [
            'names' => [
                'index'   => 'team.task.index',
                'show'    => 'team.task.show',
                'edit'    => 'team.task.edit',
                'update'  => 'team.task.update',
            ]
        ]);

        Route::resource('/manage/client', TeamClientController::class, [
            'names' => [
                'index'   => 'team.client.index',
                'create'  => 'team.client.create',
                'store'   => 'team.client.store',
                'show'    => 'team.client.show',
                'edit'    => 'team.client.edit',
                'update'  => 'team.client.update',
                'destroy' => 'team.client.destroy',
            ]
        ]);
        Route::delete('/manage/client/{client}/attachment/{attachment}', [TeamClientAttachmentController::class, 'destroy'])->name('team.client.attachments.destroy');

        Route::resource('/manage/cases', TeamCasesController::class, [
            'names' => [
                'index'     => 'team.cases.index',
                'create'    => 'team.cases.create',
                'store'     => 'team.cases.store',
                'show'      => 'team.cases.show',
                'edit'      => 'team.cases.edit',
                'update'    => 'team.cases.update',
                'destroy'   => 'team.cases.destroy',
                ]
        ]);
        Route::delete('/manage/cases/{case}/attachment/{attachment}', [TeamCaseAttachmentController::class, 'destroy'])->name('team.cases.attachments.destroy');
        Route::patch('/cases/{caseId}/hearing/{hearingId}', [TeamCasesController::class, 'changeHearingDate'])->name('team.cases.hearing');


        Route::resource('/manage/appointments', TeamAppointmentController::class, [
            'names' => [
                'index'   => 'team.appointment.index',
                'create'  => 'team.appointment.create',
                'store'   => 'team.appointment.store',
                'show'    => 'team.appointment.show',
                'edit'    => 'team.appointment.edit',
                'update'  => 'team.appointment.update',
                'destroy' => 'team.appointment.destroy',
            ]
        ]);

        // // Event calendar
        Route::get('/calendar/events', [TeamCalendarController::class, 'events'])->name('team.calendar.events');
        // Route::resource('/calendar', FirmCalendarController::class, [
        //     'names' => [
        //         'index'             => 'firm.calendar.index',
        //         'create'            => 'firm.calendar.create',
        //         'store'             => 'firm.calendar.store',
        //         'show'              => 'firm.calendar.show',
        //         'edit'              => 'firm.calendar.edit',
        //         'update'            => 'firm.calendar.update',
        //         'destroy'           => 'firm.calendar.destroy',
        //     ]
        // ]);

        // // Billing & Invoice
        Route::resource('/invoice', TeamInvoiceController::class, [
            'names' => [
                'index'             => 'team.invoice.index',
                'create'            => 'team.invoice.create',
                'store'             => 'team.invoice.store',
                'show'              => 'team.invoice.show',
                'edit'              => 'team.invoice.edit',
                'update'            => 'team.invoice.update',
                'destroy'           => 'team.invoice.destroy',
            ]
        ]);

    });

    // notifications
    Route::get('/notification',                     [TeamNotificationController::class, 'getNotifications']);
    Route::patch('/notification/{notification}',    [TeamNotificationController::class, 'markRead']);

    Route::get('/signout', [AuthController::class, 'signout'])->name('team.signout');

    // aerodrop
    Route::post('/upload', [AeroDropController::class, 'upload']);
});

/*
|--------------------------------------------------------------------------
| 🔒 AUTH ROUTES 🔒
|--------------------------------------------------------------------------
| These routes are designated for authentication.
| 'guest' middleware ensures only unauthenticated users can access.
|--------------------------------------------------------------------------
*/

Route::group(['middleware' => 'guest'], function () {

    Route::view('/signin', 'auth.signin')->name('signin');
    Route::post('/signin', [AuthController::class, 'signin']);

    Route::view('/signup', 'auth.signup')->name('signup');
    Route::post('/signup', [AuthController::class, 'signup']);

    Route::view('/forgot', 'auth.forgot')->name('forgot');
    Route::post('/forgot', [AuthController::class, 'forgot']);

    Route::view('/reset/{accountType}/{token}', 'auth.reset')->name('reset');
    Route::post('/reset', [AuthController::class, 'reset'])->name('reset');

    Route::get('/verify/{token}', [AuthController::class, 'verify']);
    Route::get('/verification/resend', [AuthController::class, 'sendActivationLink'])->name('verification.resend');

    // Route::post('/otp/send', [OTPController::class, 'sendOTP'])->name('otp.send');

    // Route::post('/otp/verify', [OTPController::class, 'verifyOTP'])->name('otp.verify');
});