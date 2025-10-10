<?php

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;

// Main Quiz App (Vue SPA)
Route::get('/', function () {
    return view('welcome');
});

// Admin Routes (Inertia)
Route::prefix('admin')->group(function () {
    // Public routes (no auth required)
    Route::get('/login', [AdminController::class, 'login'])->name('admin.login');
    Route::post('/login', [AdminController::class, 'authenticate'])->name('admin.authenticate');
    
    // Protected routes (require admin auth)
    Route::middleware(['admin'])->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/dashboard-data', [AdminController::class, 'dashboardData'])->name('admin.dashboard.data');
        Route::get('/participants', [AdminController::class, 'participants'])->name('admin.participants');
        Route::get('/records', [AdminController::class, 'records'])->name('admin.records');
        Route::get('/records-data', [AdminController::class, 'getRecordsData'])->name('admin.records.data');
        Route::get('/quizzes', [AdminController::class, 'quizzes'])->name('admin.quizzes');
        Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout');
    });
});

// Keep your existing API routes
require __DIR__.'/api.php';