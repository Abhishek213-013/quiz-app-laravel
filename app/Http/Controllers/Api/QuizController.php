<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizSet;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index()
    {
        try {
            $quizzes = Quiz::all();
            return response()->json($quizzes);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch quizzes'], 500);
        }
    }

    public function getByQuizSet($quizSetId)
    {
        try {
            $quizzes = Quiz::where('quiz_set_id', $quizSetId)->get();
            return response()->json($quizzes);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch questions'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'quiz_set_id' => 'required|exists:quiz_sets,id',
                'question' => 'required|string',
                'question_type' => 'required|in:multiple_choice,brief_answer,true_false',
                'options' => 'required_if:question_type,multiple_choice|array',
                'correct_answer' => 'required|string',
                'points' => 'required|integer|min:1'
            ]);

            // For non-multiple choice questions, set options to null
            if ($validated['question_type'] !== 'multiple_choice') {
                $validated['options'] = null;
            } else {
                // Ensure we have exactly 4 options for multiple choice
                $validated['options'] = array_slice($validated['options'], 0, 4);
            }

            $quiz = Quiz::create($validated);
            return response()->json($quiz, 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create question: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $quiz = Quiz::findOrFail($id);
            
            $validated = $request->validate([
                'question' => 'required|string',
                'question_type' => 'required|in:multiple_choice,brief_answer,true_false',
                'options' => 'required_if:question_type,multiple_choice|array',
                'correct_answer' => 'required|string',
                'points' => 'required|integer|min:1'
            ]);

            // For non-multiple choice questions, set options to null
            if ($validated['question_type'] !== 'multiple_choice') {
                $validated['options'] = null;
            } else {
                // Ensure we have exactly 4 options for multiple choice
                $validated['options'] = array_slice($validated['options'], 0, 4);
            }

            $quiz->update($validated);
            return response()->json($quiz);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update question: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $quiz = Quiz::findOrFail($id);
            $quiz->delete();
            
            return response()->json(['message' => 'Question deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete question: ' . $e->getMessage()], 500);
        }
    }
}