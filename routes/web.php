<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DailyWorkReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SystemSettingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    // Shared / Self-Service Routes (Accessible by both Employees & Admins)
    Route::get('/attendances/report', [AttendanceController::class, 'report'])->name('attendances.report');
    Route::post('/attendances/punch', [AttendanceController::class, 'punch'])->name('attendances.punch');
    Route::resource('attendances', AttendanceController::class)->only(['index']);

    Route::resource('leaves', LeaveRequestController::class)->only(['index', 'store']);
    Route::get('/payrolls/{payroll}/payslip', [PayrollController::class, 'payslip'])->name('payrolls.payslip');
    Route::resource('payrolls', PayrollController::class)->only(['index']);
    Route::resource('work-reports', DailyWorkReportController::class)->only(['index', 'store', 'destroy']);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // HR Administrator & Superadmin Only Routes
    Route::middleware(['admin'])->group(function () {
        Route::resource('departments', DepartmentController::class)->except(['create', 'show', 'edit']);
        Route::resource('designations', DesignationController::class)->except(['create', 'show', 'edit']);
        Route::resource('employees', EmployeeController::class);

        // Attendance Admin Operations
        Route::post('/attendances/batch', [AttendanceController::class, 'batch'])->name('attendances.batch');
        Route::resource('attendances', AttendanceController::class)->only(['store', 'update', 'destroy']);

        // Leave Admin Operations
        Route::post('/leaves/{leave}/approve', [LeaveRequestController::class, 'approve'])->name('leaves.approve');
        Route::post('/leaves/{leave}/reject', [LeaveRequestController::class, 'reject'])->name('leaves.reject');
        Route::delete('/leaves/{leave}', [LeaveRequestController::class, 'destroy'])->name('leaves.destroy');

        // Payroll & Salary Admin Operations
        Route::post('/payrolls/generate', [PayrollController::class, 'generate'])->name('payrolls.generate');
        Route::post('/payrolls/salary-structure', [PayrollController::class, 'storeSalaryStructure'])->name('payrolls.salary-structure');
        Route::post('/payrolls/{payroll}/payout', [PayrollController::class, 'updatePayout'])->name('payrolls.payout');
        Route::delete('/payrolls/{payroll}', [PayrollController::class, 'destroy'])->name('payrolls.destroy');

        // System Settings
        Route::get('/settings', [SystemSettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SystemSettingController::class, 'update'])->name('settings.update');

        // Dynamic Daily Work Report Fields
        Route::post('/work-reports/custom-fields', [DailyWorkReportController::class, 'storeCustomField'])->name('work-reports.custom-fields.store');
        Route::put('/work-reports/custom-fields/{key}', [DailyWorkReportController::class, 'updateCustomField'])->name('work-reports.custom-fields.update');
        Route::post('/work-reports/custom-fields/{key}/toggle', [DailyWorkReportController::class, 'toggleField'])->name('work-reports.custom-fields.toggle');
        Route::delete('/work-reports/custom-fields/{key}', [DailyWorkReportController::class, 'destroyCustomField'])->name('work-reports.custom-fields.destroy');
    });
});

require __DIR__.'/auth.php';
