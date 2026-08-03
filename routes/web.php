<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    
    // Home Page
    Route::get('/', function () {
        return view('welcome');
    })->name('home');

    // Redirect /dashboard to the appropriate role-based dashboard
    Route::get('/dashboard', function () {
        return redirect()->route(Auth::user()->dashboardRoute());
    })->name('dashboard');

    // Role-protected dashboards
    Route::middleware('role:super_admin')->group(function () {
        Route::get('/super-admin/dashboard', [DashboardController::class, 'superAdmin'])->name('dashboard.super-admin');
    });

    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('dashboard.admin');
    });

    Route::middleware('role:encoder')->group(function () {
        Route::get('/encoder/dashboard', [DashboardController::class, 'encoder'])->name('dashboard.encoder');
        Route::get('/students', [App\Http\Controllers\StudentController::class, 'index'])->name('students.index');
        Route::get('/students/create', [App\Http\Controllers\StudentController::class, 'create'])->name('students.create');
        Route::get('/students/sbfp', [App\Http\Controllers\StudentController::class, 'sbfpIndex'])->name('students.sbfp');
        Route::patch('/students/{student}/approval', [App\Http\Controllers\StudentController::class, 'updateApproval'])->name('students.approval');
        Route::post('/students', [App\Http\Controllers\StudentController::class, 'store'])->name('students.store');
        Route::delete('/students/{student}', [App\Http\Controllers\StudentController::class, 'destroy'])->name('students.destroy');
        Route::get('/students/{student}/id-card', [App\Http\Controllers\StudentController::class, 'generateIdCard'])->name('students.id-card');
        Route::get('/students/print/batch', [App\Http\Controllers\StudentController::class, 'printBatch'])->name('students.print-batch');
        
        Route::get('/attendance', [App\Http\Controllers\AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/attendance/scan', [App\Http\Controllers\AttendanceController::class, 'scan'])->name('attendance.scan');
        Route::post('/attendance/update', [App\Http\Controllers\AttendanceController::class, 'updateStatus'])->name('attendance.update');
    });

    // Account & Profile
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});