<?php

use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\ConsolidatedReportController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DataResetController;
use App\Http\Controllers\Admin\ResidentVerificationController as AdminResidentVerificationController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\CurfewLogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PerformanceMetricsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfilePhotoController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReportPhotoController;
use App\Http\Controllers\ResidentVerificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
});

Route::view('/privacy', 'legal.privacy')->name('privacy');
Route::view('/terms', 'legal.terms')->name('terms');
Route::post('/performance-metrics', [PerformanceMetricsController::class, 'store'])
    ->middleware('throttle:60,1')
    ->name('performance.metrics');

Route::get('/locale/{locale}', function (string $locale) {
    abort_unless(in_array($locale, ['en', 'tl']), 404);

    session(['locale' => $locale]);

    return redirect()->back();
})->middleware(['auth', 'role:resident,tanod,admin,official'])->name('locale.switch');

Route::get('/dashboard', [HomeController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/photo', [ProfilePhotoController::class, 'show'])->name('profile.photo');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/verification', [ResidentVerificationController::class, 'store'])->name('profile.verification.store')->middleware('throttle:5,1');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin,official'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/verifications', [AdminResidentVerificationController::class, 'index'])->name('verifications.index');
    Route::get('/verifications/{verification}/document', [AdminResidentVerificationController::class, 'document'])->name('verifications.document');
    Route::get('/verifications/{verification}/avatar', [AdminResidentVerificationController::class, 'avatar'])->name('verifications.avatar');
    Route::patch('/verifications/{verification}', [AdminResidentVerificationController::class, 'decide'])->name('verifications.decide');
    Route::get('/staff/create', [StaffController::class, 'create'])->name('staff.create');
    Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
    Route::get('/analytics/insights', [AnalyticsController::class, 'insights'])->name('analytics.insights');
    Route::get('/analytics/export/pdf', [AnalyticsController::class, 'exportPdf'])->name('analytics.exportPdf');
});

Route::middleware(['auth', 'role:tanod,admin,official'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/reports/export/pdf', [DashboardController::class, 'exportReportsPdf'])->name('reports.exportPdf');
    Route::patch('/reports/{report}/status', [DashboardController::class, 'updateStatus'])->name('reports.updateStatus');
    Route::patch('/reports/{report}', [DashboardController::class, 'updateQueueReport'])->name('reports.update');
    Route::patch('/reports/{report}/assign', [DashboardController::class, 'assign'])->name('reports.assign')->middleware('role:admin,official');
    Route::patch('/reports/{report}/severity', [DashboardController::class, 'updateSeverity'])->name('reports.updateSeverity')->middleware('role:admin,official');
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('auditLogs')->middleware('role:admin,official');
    Route::get('/audit-logs/export/pdf', [AuditLogController::class, 'exportPdf'])->name('auditLogs.exportPdf')->middleware('role:admin,official');
    Route::delete('/data/reset', DataResetController::class)->name('data.reset')->middleware('role:admin');
    Route::get('/reports/consolidated', [ConsolidatedReportController::class, 'index'])->name('consolidatedReports');
    Route::get('/reports/consolidated/export/pdf', [ConsolidatedReportController::class, 'exportPdf'])->name('consolidatedReports.exportPdf');
    Route::get('/reports/{report}/details', [DashboardController::class, 'showDetails'])->name('reports.details');
    Route::get('/reports/{report}/photo', [ReportPhotoController::class, 'show'])->name('reports.photo');
    Route::get('/reports/{report}/curfew-details', [DashboardController::class, 'showCurfewDetails'])->name('reports.curfewDetails');
});

Route::middleware(['auth', 'role:resident,admin,official'])->group(function () {
    Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store')->middleware('throttle:10,1');
    Route::post('/reports/suggest-category', [ReportController::class, 'suggestCategory'])->name('reports.suggestCategory')->middleware(['throttle:20,1', 'throttle:120,60,suggest_hour']);
    Route::get('/reports', [ReportController::class, 'myReports'])->name('reports.index');
    Route::get('/reports/{report}/photo', [ReportPhotoController::class, 'show'])->name('reports.photo');
    Route::get('/reports/{report}/assigned-tanod/photo', [ReportPhotoController::class, 'assignedTanodAvatar'])->name('reports.assignedTanod.photo');
    Route::post('/reports/{report}/confirm', [ReportController::class, 'confirmFixed'])->name('reports.confirm')->middleware('throttle:10,1');
    Route::post('/reports/{report}/reopen', [ReportController::class, 'reopen'])->name('reports.reopen')->middleware('throttle:10,1');
});

Route::middleware(['auth', 'role:resident,admin,official'])->group(function () {
    Route::post('/chatbot/send', [ChatbotController::class, 'send'])->name('chatbot.send')->middleware(['throttle:20,1', 'throttle:150,60,chatbot_hour']);
});

Route::middleware(['auth', 'role:tanod,admin'])->group(function () {
    Route::get('/curfew/create', [CurfewLogController::class, 'create'])->name('curfew.create');
    Route::post('/curfew', [CurfewLogController::class, 'store'])->name('curfew.store');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.readAll');
    Route::post('/notifications/{notification}/open', [NotificationController::class, 'open'])->name('notifications.open');
});

require __DIR__.'/auth.php';
