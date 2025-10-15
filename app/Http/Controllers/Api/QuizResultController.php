<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QuizResult;
use Illuminate\Http\Request;

class QuizResultController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'quiz_set_id' => 'required|exists:quiz_sets,id',
            'participant_name' => 'required|string|max:255',
            'score' => 'required|integer',
            'total_questions' => 'required|integer',
            'answers' => 'required|array',
            'time_taken' => 'required|integer',
            'browser_id' => 'nullable|string|max:255' // ← ADD THIS VALIDATION
        ]);

        $percentage = ($request->score / $request->total_questions) * 100;

        $quizResult = QuizResult::create([
            'quiz_set_id' => $request->quiz_set_id,
            'participant_name' => $request->participant_name,
            'score' => $request->score,
            'total_questions' => $request->total_questions,
            'answers' => $request->answers,
            'percentage' => round($percentage, 2),
            'time_taken' => $request->time_taken,
            'browser_id' => $request->browser_id // ← ADD THIS LINE
        ]);

        return response()->json($quizResult, 201);
    }

    public function index()
    {
        $results = QuizResult::with('quizSet')->orderBy('created_at', 'desc')->get();
        return response()->json($results);
    }

    public function filter(Request $request)
    {
        $query = QuizResult::with('quizSet');

        if ($request->has('participant_name') && $request->participant_name) {
            $query->where('participant_name', 'like', '%' . $request->participant_name . '%');
        }

        if ($request->has('quiz_set_id') && $request->quiz_set_id) {
            $query->where('quiz_set_id', $request->quiz_set_id);
        }

        if ($request->has('date') && $request->date) {
            $query->whereDate('created_at', $request->date);
        }

        $results = $query->orderBy('created_at', 'desc')->get();
        return response()->json($results);
    }
}