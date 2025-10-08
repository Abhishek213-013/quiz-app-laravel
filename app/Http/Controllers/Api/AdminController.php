<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizSet;
use App\Models\QuizResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    private $secretKey = 'admin123'; 

    // Admin Authentication
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

    // Dashboard Statistics
    public function getDashboardStats()
    {
        $totalParticipants = QuizResult::distinct('participant_name')->count('participant_name');
        $totalQuizSets = QuizSet::count();
        $totalAttempts = QuizResult::count();
        
        // Daily participation data for charts
        $dailyParticipation = QuizResult::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as attempts')
        )
        ->where('created_at', '>=', now()->subDays(30))
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        // Quiz set popularity
        $quizSetStats = QuizResult::select(
            'quiz_sets.name',
            DB::raw('COUNT(quiz_results.id) as attempts'),
            DB::raw('AVG(quiz_results.percentage) as avg_score')
        )
        ->join('quiz_sets', 'quiz_results.quiz_set_id', '=', 'quiz_sets.id')
        ->groupBy('quiz_sets.id', 'quiz_sets.name')
        ->orderBy('attempts', 'desc')
        ->limit(5)
        ->get();

        // Recent attempts
        $recentAttempts = QuizResult::with(['quizSet'])
            ->latest()
            ->limit(10)
            ->get();

        return response()->json([
            'totalParticipants' => $totalParticipants,
            'totalQuizSets' => $totalQuizSets,
            'totalAttempts' => $totalAttempts,
            'dailyParticipation' => $dailyParticipation,
            'quizSetStats' => $quizSetStats,
            'recentAttempts' => $recentAttempts
        ]);
    }

    // Participants Management
    public function getParticipants()
    {
        $participants = QuizResult::select(
            'participant_name',
            DB::raw('COUNT(*) as attempts_count'),
            DB::raw('MAX(created_at) as last_attempt'),
            DB::raw('AVG(percentage) as avg_score')
        )
        ->groupBy('participant_name')
        ->orderBy('last_attempt', 'desc')
        ->get();

        return response()->json(['data' => $participants]);
    }

    public function getParticipantDetail($name)
    {
        $participantResults = QuizResult::with('quizSet')
            ->where('participant_name', $name)
            ->orderBy('created_at', 'desc')
            ->get();

        $performanceStats = QuizResult::select(
            'quiz_set_id',
            'quiz_sets.name',
            DB::raw('COUNT(*) as attempts'),
            DB::raw('MAX(score) as best_score'),
            DB::raw('AVG(percentage) as avg_percentage')
        )
        ->join('quiz_sets', 'quiz_results.quiz_set_id', '=', 'quiz_sets.id')
        ->where('participant_name', $name)
        ->groupBy('quiz_set_id', 'quiz_sets.name')
        ->get();

        return response()->json([
            'participant' => ['name' => $name],
            'results' => $participantResults,
            'performanceStats' => $performanceStats
        ]);
    }

    // Records Management
    public function getAllRecords()
    {
        $records = QuizResult::with('quizSet')
            ->latest()
            ->paginate(20);

        return response()->json($records);
    }

    public function getDailyParticipation()
    {
        $dailyStats = QuizResult::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as total_attempts'),
            DB::raw('COUNT(DISTINCT participant_name) as unique_participants')
        )
        ->where('created_at', '>=', now()->subDays(30))
        ->groupBy('date')
        ->orderBy('date', 'desc')
        ->get();

        $quizSetDaily = QuizResult::select(
            'quiz_sets.name',
            DB::raw('DATE(quiz_results.created_at) as date'),
            DB::raw('COUNT(*) as attempts')
        )
        ->join('quiz_sets', 'quiz_results.quiz_set_id', '=', 'quiz_sets.id')
        ->where('quiz_results.created_at', '>=', now()->subDays(7))
        ->groupBy('quiz_sets.name', 'date')
        ->orderBy('date', 'desc')
        ->get();

        return response()->json([
            'dailyStats' => $dailyStats,
            'quizSetDaily' => $quizSetDaily
        ]);
    }

    // Quiz Sets Management
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

    // Quizzes/Questions Management
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