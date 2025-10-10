<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QuizSet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizSetController extends Controller
{
    // Public method for frontend app
    public function index()
    {
        try {
            $quizSets = QuizSet::where('is_active', true)
                ->select('id', 'name', 'category', 'description', 'time_limit', 'question_count')
                ->get();
                
            return response()->json($quizSets);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch quiz sets'], 500);
        }
    }

    // Admin method for management (all quiz sets)
    public function adminIndex()
    {
        try {
            $quizSets = QuizSet::all();
            return response()->json($quizSets);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch quiz sets'], 500);
        }
    }

    public function show($id)
    {
        try {
            $quizSet = QuizSet::findOrFail($id);
            return response()->json($quizSet);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Quiz set not found'], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'category' => 'required|string|max:255',
                'description' => 'required|string',
                'time_limit' => 'required|integer|min:1',
                'question_count' => 'required|integer|min:1'
            ]);

            $quizSet = QuizSet::create($validated);
            return response()->json($quizSet, 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create quiz set: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $quizSet = QuizSet::findOrFail($id);
            
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'category' => 'required|string|max:255',
                'description' => 'required|string',
                'time_limit' => 'required|integer|min:1',
                'question_count' => 'required|integer|min:1'
            ]);

            $quizSet->update($validated);
            return response()->json($quizSet);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update quiz set'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $quizSet = QuizSet::findOrFail($id);
                // Delete related quizzes first
                $quizSet->quizzes()->delete();
                // Then delete the quiz set
                $quizSet->delete();
            });
            
            return response()->json(['message' => 'Quiz set deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete quiz set: ' . $e->getMessage()], 500);
        }
    }
}