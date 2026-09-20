<?php

use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\CurfewLogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [HomeController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/staff/create', [StaffController::class, 'create'])->name('staff.create');
    Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
});

Route::middleware(['auth', 'role:admin,official'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::patch('/reports/{report}/status', [DashboardController::class, 'updateStatus'])->name('reports.updateStatus');
    Route::patch('/reports/{report}/assign', [DashboardController::class, 'assign'])->name('reports.assign');
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
    Route::get('/reports/{report}/curfew-details', [DashboardController::class, 'showCurfewDetails'])->name('reports.curfewDetails');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    Route::get('/reports', [ReportController::class, 'myReports'])->name('reports.index');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/chatbot', [ChatbotController::class, 'widget'])->name('chatbot.widget');
    Route::post('/chatbot/send', [ChatbotController::class, 'send'])->name('chatbot.send');
});

Route::middleware(['auth', 'role:tanod,admin'])->group(function () {
    Route::get('/curfew/create', [CurfewLogController::class, 'create'])->name('curfew.create');
    Route::post('/curfew', [CurfewLogController::class, 'store'])->name('curfew.store');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
});

require __DIR__.'/auth.php';