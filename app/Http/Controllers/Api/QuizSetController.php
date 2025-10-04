<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QuizSet;
use Illuminate\Http\Request;

class QuizSetController extends Controller
{
    public function index()
    {
        $quizSets = QuizSet::where('is_active', true)->get();
        return response()->json($quizSets);
    }

    public function show($id)
    {
        $quizSet = QuizSet::with('quizzes')->find($id);
        
        if (!$quizSet) {
            return response()->json(['error' => 'Quiz set not found'], 404);
        }

        return response()->json($quizSet);
    }
}