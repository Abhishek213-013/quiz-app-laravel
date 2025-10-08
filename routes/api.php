<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\QuizSetController;
use App\Http\Controllers\Api\QuizController;
use App\Http\Controllers\Api\QuizResultController;
use App\Http\Controllers\Api\AdminController;

// Public API Routes (no authentication required)
Route::get('/quiz-sets', [QuizSetController::class, 'index']);
Route::get('/quiz-sets/{id}', [QuizSetController::class, 'show']);
Route::get('/quiz-sets/{id}/quizzes', [QuizController::class, 'getByQuizSet']);
Route::get('/quiz-results', [QuizResultController::class, 'index']);
Route::get('/quiz-results/filter', [QuizResultController::class, 'filter']);
Route::post('/quiz-results', [QuizResultController::class, 'store']);

// Admin Authentication
Route::post('/admin/verify', [AdminController::class, 'verify']);

// Admin Routes (Protected with secret key)
Route::middleware('admin.auth')->prefix('admin')->group(function () {
    // Dashboard & Analytics
    Route::get('/dashboard/stats', [AdminController::class, 'getDashboardStats']);
    
    // Participants Management
    Route::get('/participants', [AdminController::class, 'getParticipants']);
    Route::get('/participants/{name}', [AdminController::class, 'getParticipantDetail']);
    
    // Records & Analytics
    Route::get('/records', [AdminController::class, 'getAllRecords']);
    Route::get('/records/daily', [AdminController::class, 'getDailyParticipation']);
    
    // Quiz Sets Management
    Route::post('/quiz-sets', [AdminController::class, 'storeQuizSet']);
    Route::put('/quiz-sets/{id}', [AdminController::class, 'updateQuizSet']);
    Route::delete('/quiz-sets/{id}', [AdminController::class, 'deleteQuizSet']);
    
    // Quizzes/Questions Management
    Route::post('/quizzes', [AdminController::class, 'storeQuiz']);
    Route::put('/quizzes/{id}', [AdminController::class, 'updateQuiz']);
    Route::delete('/quizzes/{id}', [AdminController::class, 'deleteQuiz']);
});