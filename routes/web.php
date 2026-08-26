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

    // Shared Student Print/ID & School Year Switch
    Route::get('/students/{student}/id-card', [App\Http\Controllers\StudentController::class, 'generateIdCard'])->name('students.id-card');
    Route::get('/students/print/batch', [App\Http\Controllers\StudentController::class, 'printBatch'])->name('students.print-batch');
    Route::post('/school-years/switch', [App\Http\Controllers\Admin\SchoolYearController::class, 'switch'])->name('school-years.switch');



    // Role-protected dashboards
    Route::middleware('role:super_admin')->prefix('super-admin')->name('super-admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'superAdmin'])->name('dashboard');
        Route::get('/accounts', [App\Http\Controllers\Admin\AccountController::class, 'index'])->name('accounts.index');
        Route::get('/accounts/create', [App\Http\Controllers\Admin\AccountController::class, 'create'])->name('accounts.create');
        Route::post('/accounts', [App\Http\Controllers\Admin\AccountController::class, 'store'])->name('accounts.store');

        Route::get('/students', [App\Http\Controllers\Admin\StudentViewController::class, 'index'])->name('students.index');
        Route::get('/students/sbfp', [App\Http\Controllers\Admin\StudentViewController::class, 'sbfpIndex'])->name('students.sbfp');
        Route::get('/students/promote', [App\Http\Controllers\Admin\StudentPromotionController::class, 'index'])->name('students.promote');
        Route::post('/students/promote', [App\Http\Controllers\Admin\StudentPromotionController::class, 'store'])->name('students.promote.store');
        Route::get('/sections', [App\Http\Controllers\Admin\SectionController::class, 'index'])->name('sections.index');
        Route::post('/sections', [App\Http\Controllers\Admin\SectionController::class, 'store'])->name('sections.store');
        Route::post('/sections/carry-over', [App\Http\Controllers\Admin\SectionController::class, 'carryOver'])->name('sections.carry-over');
        Route::put('/sections/{section}', [App\Http\Controllers\Admin\SectionController::class, 'update'])->name('sections.update');
        Route::delete('/sections/{section}', [App\Http\Controllers\Admin\SectionController::class, 'destroy'])->name('sections.destroy');
        Route::get('/school-years', [App\Http\Controllers\Admin\SchoolYearController::class, 'index'])->name('school-years.index');
        Route::post('/school-years', [App\Http\Controllers\Admin\SchoolYearController::class, 'store'])->name('school-years.store');
        Route::post('/school-years/{schoolYear}/activate', [App\Http\Controllers\Admin\SchoolYearController::class, 'activate'])->name('school-years.activate');
        Route::get('/audit-logs', [App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('audit-logs.index');

        Route::get('/settings', [App\Http\Controllers\AccountSettingsController::class, 'edit'])->name('settings');
        Route::patch('/settings', [App\Http\Controllers\AccountSettingsController::class, 'update'])->name('settings.update');
        Route::put('/password', [App\Http\Controllers\AccountSettingsController::class, 'updatePassword'])->name('password.update');
    });

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
        Route::get('/reports', [App\Http\Controllers\ReportsController::class, 'admin'])->name('reports');
        Route::get('/accounts', [App\Http\Controllers\Admin\AccountController::class, 'index'])->name('accounts.index');
        Route::get('/accounts/create', [App\Http\Controllers\Admin\AccountController::class, 'create'])->name('accounts.create');
        Route::post('/accounts', [App\Http\Controllers\Admin\AccountController::class, 'store'])->name('accounts.store');
        Route::get('/students', [App\Http\Controllers\Admin\StudentViewController::class, 'index'])->name('students.index');
        Route::get('/students/sbfp', [App\Http\Controllers\Admin\StudentViewController::class, 'sbfpIndex'])->name('students.sbfp');
        Route::get('/sections', [App\Http\Controllers\Admin\SectionController::class, 'index'])->name('sections.index');
        Route::post('/sections', [App\Http\Controllers\Admin\SectionController::class, 'store'])->name('sections.store');
        Route::post('/sections/carry-over', [App\Http\Controllers\Admin\SectionController::class, 'carryOver'])->name('sections.carry-over');
        Route::put('/sections/{section}', [App\Http\Controllers\Admin\SectionController::class, 'update'])->name('sections.update');
        Route::delete('/sections/{section}', [App\Http\Controllers\Admin\SectionController::class, 'destroy'])->name('sections.destroy');
        Route::get('/audit-logs', [App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('audit-logs.index');

        Route::get('/settings', [App\Http\Controllers\AccountSettingsController::class, 'edit'])->name('settings');
        Route::patch('/settings', [App\Http\Controllers\AccountSettingsController::class, 'update'])->name('settings.update');
        Route::put('/password', [App\Http\Controllers\AccountSettingsController::class, 'updatePassword'])->name('password.update');
    });

    Route::middleware('role:super_admin|admin')->group(function () {
        Route::patch('/accounts/{user}/toggle-status', [App\Http\Controllers\Admin\AccountController::class, 'toggleStatus'])->name('accounts.toggle-status');
        Route::delete('/accounts/{user}', [App\Http\Controllers\Admin\AccountController::class, 'destroy'])->name('accounts.destroy');
    });

    Route::middleware('role:encoder')->prefix('encoder')->name('encoder.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'encoder'])->name('dashboard');
        Route::get('/students', [App\Http\Controllers\StudentController::class, 'index'])->name('students.index');
        Route::get('/students/create', [App\Http\Controllers\StudentController::class, 'create'])->name('students.create');
        Route::get('/students/sbfp', [App\Http\Controllers\StudentController::class, 'sbfpIndex'])->name('students.sbfp');
        Route::patch('/students/{student}/approval', [App\Http\Controllers\StudentController::class, 'updateApproval'])->name('students.approval');
        Route::post('/students', [App\Http\Controllers\StudentController::class, 'store'])->name('students.store');
        Route::delete('/students/{student}', [App\Http\Controllers\StudentController::class, 'destroy'])->name('students.destroy');
        Route::get('/students/{student}/id-card', [App\Http\Controllers\StudentController::class, 'generateIdCard'])->name('students.id-card');
        Route::get('/students/print/batch', [App\Http\Controllers\StudentController::class, 'printBatch'])->name('students.print-batch');
        Route::post('/students/{student}/assessment', [App\Http\Controllers\StudentController::class, 'storeAssessment'])->name('students.assessment');
        Route::post('/students/{student}/email-feeding', [App\Http\Controllers\StudentController::class, 'emailFeedingNotice'])->name('students.email-feeding');

        Route::get('/attendance', [App\Http\Controllers\AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/attendance/scan', [App\Http\Controllers\AttendanceController::class, 'scan'])->name('attendance.scan');
        Route::post('/attendance/update', [App\Http\Controllers\AttendanceController::class, 'updateStatus'])->name('attendance.update');

        Route::get('/settings', [App\Http\Controllers\AccountSettingsController::class, 'edit'])->name('settings');
        Route::patch('/settings', [App\Http\Controllers\AccountSettingsController::class, 'update'])->name('settings.update');
        Route::put('/password', [App\Http\Controllers\AccountSettingsController::class, 'updatePassword'])->name('password.update');
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