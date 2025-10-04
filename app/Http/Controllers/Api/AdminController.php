<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizSet;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    private $secretKey = 'admin123'; // Change this to a more secure key

    public function verify(Request $request)
    {
        $request->validate([
            'secret_key' => 'required|string'
        ]);

        if ($request->secret_key === $this->secretKey) {
            return response()->json(['success' => true]);
        }

        return response()->json(['error' => 'Invalid secret key'], 401);
    }

    public function storeQuizSet(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'time_limit' => 'required|integer',
            'question_count' => 'required|integer'
        ]);

        $quizSet = QuizSet::create($request->all());

        return response()->json($quizSet, 201);
    }

    public function updateQuizSet(Request $request, $id)
    {
        $quizSet = QuizSet::find($id);
        
        if (!$quizSet) {
            return response()->json(['error' => 'Quiz set not found'], 404);
        }

        $quizSet->update($request->all());

        return response()->json($quizSet);
    }

    public function deleteQuizSet($id)
    {
        $quizSet = QuizSet::find($id);
        
        if (!$quizSet) {
            return response()->json(['error' => 'Quiz set not found'], 404);
        }

        $quizSet->delete();

        return response()->json(['message' => 'Quiz set deleted successfully']);
    }

    public function storeQuiz(Request $request)
    {
        $request->validate([
            'quiz_set_id' => 'required|exists:quiz_sets,id',
            'question' => 'required|string',
            'options' => 'required|array',
            'correct_answer' => 'required|string',
            'points' => 'required|integer'
        ]);

        $quiz = Quiz::create($request->all());

        return response()->json($quiz, 201);
    }

    public function updateQuiz(Request $request, $id)
    {
        $quiz = Quiz::find($id);
        
        if (!$quiz) {
            return response()->json(['error' => 'Quiz not found'], 404);
        }

        $quiz->update($request->all());

        return response()->json($quiz);
    }

    public function deleteQuiz($id)
    {
        $quiz = Quiz::find($id);
        
        if (!$quiz) {
            return response()->json(['error' => 'Quiz not found'], 404);
        }

        $quiz->delete();

        return response()->json(['message' => 'Quiz deleted successfully']);
    }
}