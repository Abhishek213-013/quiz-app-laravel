<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\QuizSetController;
use App\Http\Controllers\Api\QuizController;
use App\Http\Controllers\Api\QuizResultController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\DashboardController;

// Public API Routes
Route::get('/quiz-sets', [QuizSetController::class, 'index']);
Route::get('/quiz-sets/{id}', [QuizSetController::class, 'show']);
Route::get('/quiz-sets/{id}/quizzes', [QuizController::class, 'getByQuizSet']);
Route::get('/quiz-results', [QuizResultController::class, 'index']);
Route::get('/quiz-results/filter', [QuizResultController::class, 'filter']);
Route::post('/quiz-results', [QuizResultController::class, 'store']);

// Admin Authentication
Route::post('/admin/verify', [AdminController::class, 'verify']);

// Admin API Routes
Route::prefix('admin-api')->group(function () {
    // Quiz Sets Management
    Route::get('/quiz-sets', [QuizSetController::class, 'adminIndex']);
    Route::post('/quiz-sets', [QuizSetController::class, 'store']);
    Route::put('/quiz-sets/{id}', [QuizSetController::class, 'update']);
    Route::delete('/quiz-sets/{id}', [QuizSetController::class, 'destroy']);
    
    // Quizzes Management
    Route::get('/quizzes', [QuizController::class, 'index']);
    Route::post('/quizzes', [QuizController::class, 'store']);
    Route::put('/quizzes/{id}', [QuizController::class, 'update']);
    Route::delete('/quizzes/{id}', [QuizController::class, 'destroy']);
    
    // Dashboard & Analytics
    Route::get('/dashboard/stats', [AdminController::class, 'getDashboardStats']);
    Route::get('/participants', [AdminController::class, 'getParticipants']);
    Route::get('/participants/{name}', [AdminController::class, 'getParticipantDetail']);
    Route::get('/records', [AdminController::class, 'getAllRecords']);
    Route::get('/records/daily', [AdminController::class, 'getDailyParticipation']);
    Route::get('/records/data', [AdminController::class, 'recordsData']);
});

Route::prefix('admin')->group(function () {
    Route::get('/dashboard/data', [DashboardController::class, 'getDashboardData']);
    // Or if you're using web routes, use this in routes/web.php:
    // Route::get('/dashboard/data', [DashboardController::class, 'getDashboardData'])->middleware('auth');
});