<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizSet;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function getByQuizSet($quizSetId)
    {
        $quizzes = Quiz::where('quiz_set_id', $quizSetId)->get();
        return response()->json($quizzes);
    }
}