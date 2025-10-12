<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuizSet;
use App\Models\QuizResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function getDashboardData(Request $request)
    {
        Log::info('Dashboard data request received');
        
        try {
            Log::info('Starting dashboard data processing');

            // Basic stats
            $stats = [
                'total_participants' => $this->getTotalParticipants(),
                'total_quiz_sets' => $this->getTotalQuizSets(),
                'total_attempts' => $this->getTotalAttempts(),
            ];

            Log::info('Basic stats calculated', $stats);

            // Quiz set participation distribution
            $quizSetDistribution = $this->getQuizSetDistribution();
            Log::info('Quiz set distribution calculated', ['count' => count($quizSetDistribution)]);

            // Weekly participants data
            $weeklyParticipants = $this->getWeeklyParticipants();
            Log::info('Weekly participants calculated');

            // Top scorers for each quiz set
            $topScorers = $this->getTopScorers();
            Log::info('Top scorers calculated', ['count' => count($topScorers)]);

            // Recent quiz attempts
            $recentAttempts = $this->getRecentAttempts();
            Log::info('Recent attempts calculated', ['count' => count($recentAttempts)]);

            $responseData = [
                'success' => true,
                'data' => [
                    'stats' => $stats,
                    'quizSetDistribution' => $quizSetDistribution,
                    'weeklyParticipants' => $weeklyParticipants,
                    'topScorers' => $topScorers,
                    'recentAttempts' => $recentAttempts
                ]
            ];

            Log::info('Dashboard data prepared successfully');

            return response()->json($responseData);

        } catch (\Exception $e) {
            Log::error('Dashboard data error: ' . $e->getMessage());
            Log::error('Error trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard data',
                'error' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    /**
     * Get total unique participants
     */
    private function getTotalParticipants()
    {
        try {
            return QuizResult::distinct('participant_name')->count('participant_name');
        } catch (\Exception $e) {
            Log::error('Error in getTotalParticipants: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get total active quiz sets
     */
    private function getTotalQuizSets()
    {
        try {
            return QuizSet::where('is_active', true)->count();
        } catch (\Exception $e) {
            Log::error('Error in getTotalQuizSets: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get total attempts
     */
    private function getTotalAttempts()
    {
        try {
            return QuizResult::count();
        } catch (\Exception $e) {
            Log::error('Error in getTotalAttempts: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get quiz set participation distribution
     */
    private function getQuizSetDistribution()
    {
        try {
            $distribution = QuizSet::withCount('results')
                ->where('is_active', true)
                ->orderBy('results_count', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($quizSet) {
                    return [
                        'quiz_set_name' => $quizSet->name,
                        'participant_count' => $quizSet->results_count
                    ];
                })->toArray();

            return $distribution ?: [];

        } catch (\Exception $e) {
            Log::error('Error in getQuizSetDistribution: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get weekly participants data
     */
    private function getWeeklyParticipants()
    {
        try {
            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();

            $participantsByDay = QuizResult::select(
                    DB::raw('DAYNAME(created_at) as day'),
                    DB::raw('COUNT(DISTINCT participant_name) as count')
                )
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->groupBy(DB::raw('DAYNAME(created_at)'))
                ->orderBy(DB::raw('MIN(created_at)'))
                ->get();

            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            $data = array_fill(0, 7, 0);

            foreach ($participantsByDay as $item) {
                $index = array_search($item->day, $days);
                if ($index !== false) {
                    $data[$index] = $item->count;
                }
            }

            return [
                'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                'data' => $data
            ];

        } catch (\Exception $e) {
            Log::error('Error in getWeeklyParticipants: ' . $e->getMessage());
            return [
                'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                'data' => [0, 0, 0, 0, 0, 0, 0]
            ];
        }
    }

    /**
     * Get top scorers for each quiz set
     */
    private function getTopScorers()
    {
        try {
            // Get active quiz sets
            $quizSets = QuizSet::where('is_active', true)->get();
            
            $topScorers = [];

            foreach ($quizSets as $quizSet) {
                // Get top 3 results for this quiz set
                $topResults = QuizResult::where('quiz_set_id', $quizSet->id)
                    ->orderBy('percentage', 'desc')
                    ->orderBy('score', 'desc')
                    ->limit(3)
                    ->get();

                if ($topResults->count() > 0) {
                    $topScorers[] = [
                        'quiz_set_id' => $quizSet->id,
                        'quiz_set_name' => $quizSet->name,
                        'category' => $quizSet->category,
                        'total_attempts' => $quizSet->results()->count(),
                        'top_participants' => $topResults->map(function ($result) {
                            return [
                                'participant_name' => $result->participant_name,
                                'score' => $result->score,
                                'total_questions' => $result->total_questions,
                                'percentage' => $result->percentage,
                                'created_at' => $result->created_at->toISOString()
                            ];
                        })->toArray()
                    ];
                }
            }

            return $topScorers;

        } catch (\Exception $e) {
            Log::error('Error in getTopScorers: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get recent quiz attempts
     */
    private function getRecentAttempts()
    {
        try {
            $recentAttempts = QuizResult::with('quizSet')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($result) {
                    return [
                        'id' => $result->id,
                        'participant_name' => $result->participant_name,
                        'quiz_set_name' => $result->quizSet->name ?? 'Unknown Quiz Set',
                        'category' => $result->quizSet->category ?? 'Unknown',
                        'score' => $result->score,
                        'total_questions' => $result->total_questions,
                        'percentage' => $result->percentage,
                        'created_at' => $result->created_at->toISOString()
                    ];
                })->toArray();

            return $recentAttempts;

        } catch (\Exception $e) {
            Log::error('Error in getRecentAttempts: ' . $e->getMessage());
            return [];
        }
    }
}