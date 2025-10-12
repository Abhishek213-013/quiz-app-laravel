<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\CustomizeController;
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
    Route::middleware(['admin.auth'])->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/dashboard-data', [AdminController::class, 'dashboardData'])->name('admin.dashboard.data');
        
        // New pages for profile, settings, customize
        Route::get('/profile', [ProfileController::class, 'index'])->name('admin.profile');
        Route::get('/settings', [SettingsController::class, 'index'])->name('admin.settings');
        Route::get('/customize', [CustomizeController::class, 'index'])->name('admin.customize');
        
        Route::get('/participants', [AdminController::class, 'participants'])->name('admin.participants');
        Route::get('/records', [AdminController::class, 'records'])->name('admin.records');
        Route::get('/records-data', [AdminController::class, 'recordsData'])->name('admin.records.data');
        Route::get('/quizzes', [AdminController::class, 'quizzes'])->name('admin.quizzes');
        
        // Quiz Management API Routes
        Route::get('/quiz-sets', [AdminController::class, 'getQuizSets'])->name('admin.quiz-sets.get');
        Route::post('/quiz-sets', [AdminController::class, 'storeQuizSet'])->name('admin.quiz-sets.store');
        Route::put('/quiz-sets/{id}', [AdminController::class, 'updateQuizSet'])->name('admin.quiz-sets.update');
        Route::delete('/quiz-sets/{id}', [AdminController::class, 'deleteQuizSet'])->name('admin.quiz-sets.delete');
        
        Route::post('/quizzes', [AdminController::class, 'storeQuiz'])->name('admin.quizzes.store');
        Route::put('/quizzes/{id}', [AdminController::class, 'updateQuiz'])->name('admin.quizzes.update');
        Route::delete('/quizzes/{id}', [AdminController::class, 'deleteQuiz'])->name('admin.quizzes.delete');
        
        // Additional API routes
        Route::get('/participants-data', [AdminController::class, 'getParticipants'])->name('admin.participants.data');
        Route::get('/participants/{name}', [AdminController::class, 'getParticipantDetail'])->name('admin.participants.detail');
        Route::get('/daily-participation', [AdminController::class, 'getDailyParticipation'])->name('admin.daily-participation');
        Route::get('/all-records', [AdminController::class, 'getAllRecords'])->name('admin.all-records');
        
        // API routes for new pages
        Route::put('/profile/update', [ProfileController::class, 'update'])->name('admin.profile.update');
        Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('admin.profile.avatar');
        Route::put('/settings/general', [SettingsController::class, 'updateGeneral'])->name('admin.settings.general');
        Route::put('/settings/security', [SettingsController::class, 'updateSecurity'])->name('admin.settings.security');
        Route::put('/settings/notifications', [SettingsController::class, 'updateNotifications'])->name('admin.settings.notifications');
        Route::put('/customize/update', [CustomizeController::class, 'update'])->name('admin.customize.update');
        Route::post('/customize/logo', [CustomizeController::class, 'uploadLogo'])->name('admin.customize.logo');
        Route::post('/customize/favicon', [CustomizeController::class, 'uploadFavicon'])->name('admin.customize.favicon');
        Route::post('/customize/reset', [CustomizeController::class, 'reset'])->name('admin.customize.reset');
        
        Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout');
    });
});

// Keep your existing API routes
require __DIR__.'/api.php';