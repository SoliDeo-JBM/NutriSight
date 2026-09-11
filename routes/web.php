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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

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
    // Redirect /dashboard to the appropriate role-based dashboard
    Route::get('/dashboard', function () {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return redirect()->route($user->dashboardRoute());
    })->name('dashboard');

    // Shared Student Print/ID & School Year Switch
    Route::get('/students/{student}/id-card', [App\Http\Controllers\StudentController::class, 'generateIdCard'])->name('students.id-card');
    Route::get('/students/print/batch', [App\Http\Controllers\StudentController::class, 'printBatch'])->name('students.print-batch');
    Route::post('/school-years/switch', [App\Http\Controllers\Admin\SchoolYearController::class, 'switch'])->name('school-years.switch');



    // Role-protected dashboards
    Route::middleware('role:super_admin')->prefix('super-admin')->name('super-admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'superAdmin'])->name('dashboard');
        Route::get('/attendance', [App\Http\Controllers\AttendanceController::class, 'adminIndex'])->name('attendance.index');
        Route::post('/attendance/update', [App\Http\Controllers\AttendanceController::class, 'updateStatus'])->name('attendance.update');
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
        Route::patch('/school-years/{schoolYear}', [App\Http\Controllers\Admin\SchoolYearController::class, 'update'])->name('school-years.update');
        Route::delete('/school-years/{schoolYear}', [App\Http\Controllers\Admin\SchoolYearController::class, 'destroy'])->name('school-years.destroy');
        Route::post('/school-years/{schoolYear}/activate', [App\Http\Controllers\Admin\SchoolYearController::class, 'activate'])->name('school-years.activate');
        Route::get('/audit-logs', [App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('audit-logs.index');

        Route::get('/settings', [App\Http\Controllers\AccountSettingsController::class, 'edit'])->name('settings');
        Route::patch('/settings', [App\Http\Controllers\AccountSettingsController::class, 'update'])->name('settings.update');
        Route::put('/password', [App\Http\Controllers\AccountSettingsController::class, 'updatePassword'])->name('password.update');
    });

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
        Route::get('/attendance', [App\Http\Controllers\AttendanceController::class, 'adminIndex'])->name('attendance.index');
        Route::post('/attendance/update', [App\Http\Controllers\AttendanceController::class, 'updateStatus'])->name('attendance.update');
        Route::get('/meal-plans', [App\Http\Controllers\Admin\MealPlanController::class, 'index'])->name('meal-plans.index');
        Route::post('/meal-plans', [App\Http\Controllers\Admin\MealPlanController::class, 'store'])->name('meal-plans.store');
        Route::put('/meal-plans/{mealPlan}', [App\Http\Controllers\Admin\MealPlanController::class, 'update'])->name('meal-plans.update');
        Route::delete('/meal-plans/{mealPlan}', [App\Http\Controllers\Admin\MealPlanController::class, 'destroy'])->name('meal-plans.destroy');
        Route::get('/school-years', [App\Http\Controllers\Admin\SchoolYearController::class, 'index'])->name('school-years.index');
        Route::post('/school-years', [App\Http\Controllers\Admin\SchoolYearController::class, 'store'])->name('school-years.store');
        Route::patch('/school-years/{schoolYear}', [App\Http\Controllers\Admin\SchoolYearController::class, 'update'])->name('school-years.update');
        Route::delete('/school-years/{schoolYear}', [App\Http\Controllers\Admin\SchoolYearController::class, 'destroy'])->name('school-years.destroy');
        Route::post('/school-years/{schoolYear}/activate', [App\Http\Controllers\Admin\SchoolYearController::class, 'activate'])->name('school-years.activate');

        Route::prefix('reports/sbfp')->name('reports.sbfp.')->group(function () {
            Route::get('/', [App\Http\Controllers\ReportsController::class, 'sbfpIndex'])->name('index');
            Route::get('/attendance', [App\Http\Controllers\ReportsController::class, 'sbfpAttendance'])->name('attendance');
            Route::post('/attendance/months', [App\Http\Controllers\ReportsController::class, 'storeAttendanceMonth'])->name('attendance.months.store');
            Route::get('/attendance/month/{month}', [App\Http\Controllers\ReportsController::class, 'showAttendanceMonth'])->name('attendance.month');
            Route::get('/attendance/month/{month}/excel', [App\Http\Controllers\ReportsController::class, 'exportAttendanceExcel'])->name('attendance.month.excel');
            Route::get('/attendance/month/{month}/docx', [App\Http\Controllers\ReportsController::class, 'exportAttendanceDocx'])->name('attendance.month.docx');
            Route::get('/attendance/month/{month}/pdf', [App\Http\Controllers\ReportsController::class, 'exportAttendancePdf'])->name('attendance.month.pdf');
            Route::get('/attendance/month/{month}/sql', [App\Http\Controllers\ReportsController::class, 'exportAttendanceSql'])->name('attendance.month.sql');
            Route::patch('/attendance/month/{month}', [App\Http\Controllers\ReportsController::class, 'updateAttendanceMonth'])->name('attendance.month.update');
            Route::delete('/attendance/month/{month}', [App\Http\Controllers\ReportsController::class, 'destroyAttendanceMonth'])->name('attendance.month.destroy');
            Route::get('/attendance/summary/{schoolYear}', [App\Http\Controllers\ReportsController::class, 'showAttendanceSummary'])->name('attendance.summary');
            Route::get('/annual-consolidated', [App\Http\Controllers\ReportsController::class, 'sbfpYearly'])->name('annual');
            Route::post('/annual-consolidated/periods', [App\Http\Controllers\ReportsController::class, 'storeReportPeriod'])->name('annual.periods.store');
            Route::get('/annual-consolidated/summary/{schoolYear}', [App\Http\Controllers\ReportsController::class, 'showAnnualSummary'])->name('annual.summary');
            Route::get('/annual-consolidated/summary/{schoolYear}/excel', [App\Http\Controllers\ReportsController::class, 'exportAnnualSummaryExcel'])->name('annual.summary.excel');
            Route::get('/annual-consolidated/summary/{schoolYear}/docx', [App\Http\Controllers\ReportsController::class, 'exportAnnualSummaryDocx'])->name('annual.summary.docx');
            Route::get('/annual-consolidated/summary/{schoolYear}/pdf', [App\Http\Controllers\ReportsController::class, 'exportAnnualSummaryPdf'])->name('annual.summary.pdf');
            Route::get('/annual-consolidated/summary/{schoolYear}/sql', [App\Http\Controllers\ReportsController::class, 'exportAnnualSummarySql'])->name('annual.summary.sql');
            Route::get('/annual-consolidated/period/{period}', [App\Http\Controllers\ReportsController::class, 'showReportPeriod'])->name('annual.period');
            Route::patch('/annual-consolidated/period/{period}', [App\Http\Controllers\ReportsController::class, 'updateReportPeriod'])->name('annual.period.update');
            Route::delete('/annual-consolidated/period/{period}', [App\Http\Controllers\ReportsController::class, 'destroyReportPeriod'])->name('annual.period.destroy');
            Route::post('/annual-consolidated/period/{period}/generate', [App\Http\Controllers\ReportsController::class, 'generateReportPeriod'])->name('annual.period.generate');
            Route::get('/annual-consolidated/period/{period}/excel', [App\Http\Controllers\ReportsController::class, 'exportAnnualPeriodExcel'])->name('annual.period.excel');
            Route::get('/annual-consolidated/period/{period}/docx', [App\Http\Controllers\ReportsController::class, 'exportAnnualPeriodDocx'])->name('annual.period.docx');
            Route::get('/annual-consolidated/period/{period}/pdf', [App\Http\Controllers\ReportsController::class, 'exportAnnualPeriodPdf'])->name('annual.period.pdf');
            Route::get('/annual-consolidated/period/{period}/sql', [App\Http\Controllers\ReportsController::class, 'exportAnnualPeriodSql'])->name('annual.period.sql');
            Route::get('/assessment', [App\Http\Controllers\ReportsController::class, 'sbfpAssessment'])->name('assessment');
            Route::get('/assessment/excel', [App\Http\Controllers\ReportsController::class, 'exportAssessmentExcel'])->name('assessment.excel');
            Route::get('/assessment/docx', [App\Http\Controllers\ReportsController::class, 'exportAssessmentDocx'])->name('assessment.docx');
            Route::get('/assessment/pdf', [App\Http\Controllers\ReportsController::class, 'exportAssessmentPdf'])->name('assessment.pdf');
            Route::get('/assessment/sql', [App\Http\Controllers\ReportsController::class, 'exportAssessmentSql'])->name('assessment.sql');
        });

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
        Route::get('/students/{student}/edit', [App\Http\Controllers\StudentController::class, 'edit'])->name('students.edit');
        Route::get('/students/sbfp', [App\Http\Controllers\StudentController::class, 'sbfpIndex'])->name('students.sbfp');
        Route::post('/students/sbfp/profile-images', [App\Http\Controllers\StudentController::class, 'uploadProfileImages'])->name('students.sbfp.profile-images');
        Route::post('/students/assessment/bulk', [App\Http\Controllers\StudentController::class, 'storeBulkAssessments'])->name('students.assessment.bulk');
        Route::patch('/students/assessment/bulk', [App\Http\Controllers\StudentController::class, 'updateBulkAssessments'])->name('students.assessment.bulk.update');
        Route::patch('/students/{student}/assessment/period', [App\Http\Controllers\StudentController::class, 'updatePeriod'])->name('students.assessment.period.update');
        Route::patch('/students/{student}/approval', [App\Http\Controllers\StudentController::class, 'updateApproval'])->name('students.approval');
        Route::patch('/students/approval/bulk', [App\Http\Controllers\StudentController::class, 'updateBulkApproval'])->name('students.approval.bulk');
        Route::post('/students', [App\Http\Controllers\StudentController::class, 'store'])->name('students.store');
        Route::put('/students/{student}', [App\Http\Controllers\StudentController::class, 'update'])->name('students.update');
        Route::delete('/students/{student}', [App\Http\Controllers\StudentController::class, 'destroy'])->name('students.destroy');
        Route::get('/students/{student}/id-card', [App\Http\Controllers\StudentController::class, 'generateIdCard'])->name('students.id-card');
        Route::get('/students/print/batch', [App\Http\Controllers\StudentController::class, 'printBatch'])->name('students.print-batch');
        Route::post('/students/{student}/assessment', [App\Http\Controllers\StudentController::class, 'storeAssessment'])->name('students.assessment');
        Route::post('/students/{student}/email-feeding', [App\Http\Controllers\StudentController::class, 'emailFeedingNotice'])->name('students.email-feeding');

        Route::get('/settings', [App\Http\Controllers\AccountSettingsController::class, 'edit'])->name('settings');
        Route::patch('/settings', [App\Http\Controllers\AccountSettingsController::class, 'update'])->name('settings.update');
        Route::put('/password', [App\Http\Controllers\AccountSettingsController::class, 'updatePassword'])->name('password.update');
    });

    // Shared Attendance Routes
    Route::middleware('role:encoder|admin|super_admin')->name('encoder.')->group(function () {
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
