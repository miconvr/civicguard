<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\CurfewLogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/staff/create', [StaffController::class, 'create'])->name('staff.create');
    Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::patch('/reports/{report}/status', [DashboardController::class, 'updateStatus'])->name('reports.updateStatus');
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

require __DIR__.'/auth.php';