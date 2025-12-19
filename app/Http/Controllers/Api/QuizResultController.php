<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QuizResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class QuizResultController extends Controller
{
    public function store(Request $request)
    {
        Log::info('Quiz Result Submission:', $request->all());
        
        try {
            $validated = $request->validate([
                'quiz_set_id' => 'required|exists:quiz_sets,id',
                'participant_name' => 'required|string|max:255',
                'score' => 'required|integer|min:0',
                'total_questions' => 'required|integer|min:1',
                'answers' => 'required|array',
                'time_taken' => 'required|integer|min:0',
                'percentage' => 'required|numeric|min:0|max:100',
                'browser_id' => 'nullable|string|max:255',
                'answered_count' => 'sometimes|integer',
                'skipped_count' => 'sometimes|integer',
                'correct_count' => 'sometimes|integer'
            ]);

            Log::info('Validated data:', $validated);

            $quizResult = QuizResult::create([
                'quiz_set_id' => $validated['quiz_set_id'],
                'participant_name' => $validated['participant_name'],
                'score' => $validated['score'],
                'total_questions' => $validated['total_questions'],
                'answers' => $validated['answers'],
                'percentage' => $validated['percentage'],
                'time_taken' => $validated['time_taken'],
                'browser_id' => $validated['browser_id'] ?? null,
                'answered_count' => $validated['answered_count'] ?? 0,
                'skipped_count' => $validated['skipped_count'] ?? 0,
                'correct_count' => $validated['correct_count'] ?? 0
            ]);

            Log::info('Quiz Result Created:', $quizResult->toArray());

            return response()->json([
                'message' => 'Quiz result saved successfully',
                'data' => $quizResult
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation Error:', $e->errors());
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Quiz Result Error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'Server error: ' . $e->getMessage(),
                'error_type' => get_class($e)
            ], 500);
        }
    }

    public function index()
    {
        try {
            $results = QuizResult::with('quizSet')->orderBy('created_at', 'desc')->get();
            return response()->json($results);
        } catch (\Exception $e) {
            Log::error('Fetch Results Error:', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Failed to fetch results'
            ], 500);
        }
    }

    public function filter(Request $request)
    {
        try {
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
        } catch (\Exception $e) {
            Log::error('Filter Results Error:', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Failed to filter results'
            ], 500);
        }
    }
}