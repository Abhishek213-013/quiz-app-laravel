<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\QuizSetController;
use App\Http\Controllers\Api\QuizController;
use App\Http\Controllers\Api\QuizResultController;
use App\Http\Controllers\Api\AdminController;

// Public API Routes
Route::get('/quiz-sets', [QuizSetController::class, 'index']);
Route::get('/quiz-sets/{id}', [QuizSetController::class, 'show']);
Route::get('/quiz-sets/{id}/quizzes', [QuizController::class, 'getByQuizSet']);
Route::post('/quiz-results', [QuizResultController::class, 'store']);
Route::get('/quiz-results', [QuizResultController::class, 'index']);
Route::get('/quiz-results/filter', [QuizResultController::class, 'filter']);

// Admin Routes (Protected with secret key)
Route::post('/admin/verify', [AdminController::class, 'verify']);
Route::middleware('admin.auth')->group(function () {
    Route::post('/admin/quiz-sets', [AdminController::class, 'storeQuizSet']);
    Route::put('/admin/quiz-sets/{id}', [AdminController::class, 'updateQuizSet']);
    Route::delete('/admin/quiz-sets/{id}', [AdminController::class, 'deleteQuizSet']);
    Route::post('/admin/quizzes', [AdminController::class, 'storeQuiz']);
    Route::put('/admin/quizzes/{id}', [AdminController::class, 'updateQuiz']);
    Route::delete('/admin/quizzes/{id}', [AdminController::class, 'deleteQuiz']);
});