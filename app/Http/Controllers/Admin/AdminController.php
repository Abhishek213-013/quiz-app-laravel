<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function login()
    {
        // If already authenticated, redirect to dashboard
        if (Session::get('admin_authenticated')) {
            return redirect()->route('admin.dashboard');
        }
        
        return Inertia::render('Admin/Login');
    }

    public function authenticate(Request $request)
    {
        try {
            $validated = $request->validate([
                'secret_key' => 'required|string|min:3'
            ]);

            $adminSecretKey = config('app.admin_secret_key', 'admin123');
            
            if ($validated['secret_key'] === $adminSecretKey) {
                Session::put('admin_authenticated', true);
                Session::save(); // Ensure session is saved immediately
                
                return redirect()->route('admin.dashboard')->with([
                    'message' => 'Welcome to the admin panel!',
                    'type' => 'success'
                ]);
            }

            return back()->withErrors([
                'secret_key' => 'Invalid secret key. Please try again.'
            ]);

        } catch (\Exception $e) {
            return back()->withErrors([
                'secret_key' => 'Authentication failed. Please try again.'
            ]);
        }
    }

    public function dashboard()
    {
        // Check if user is authenticated
        if (!Session::get('admin_authenticated')) {
            return redirect()->route('admin.login')->withErrors([
                'message' => 'Please login to access the dashboard.'
            ]);
        }

        // Get basic stats from database
        $stats = [
            'totalParticipants' => DB::table('quiz_results')->distinct('participant_name')->count('participant_name'),
            'totalQuizSets' => DB::table('quiz_sets')->where('is_active', 1)->count(),
            'totalAttempts' => DB::table('quiz_results')->count(),
            'recentAttempts' => $this->getRecentAttempts()
        ];

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats
        ]);
    }

    public function participants()
    {
        // Check if user is authenticated
        if (!Session::get('admin_authenticated')) {
            return redirect()->route('admin.login')->withErrors([
                'message' => 'Please login to access the participants page.'
            ]);
        }

        // Get participants data from database
        $participants = DB::table('quiz_results')
            ->select(
                'participant_name',
                DB::raw('MAX(score) as best_score'),
                DB::raw('MAX(percentage) as best_percentage'),
                DB::raw('COUNT(*) as total_attempts'),
                DB::raw('AVG(percentage) as average_score'),
                DB::raw('MAX(created_at) as last_attempt')
            )
            ->groupBy('participant_name')
            ->orderBy('total_attempts', 'desc')
            ->get()
            ->map(function ($participant) {
                return [
                    'name' => $participant->participant_name,
                    'best_score' => $participant->best_score,
                    'best_percentage' => round($participant->best_percentage, 2),
                    'total_attempts' => $participant->total_attempts,
                    'average_score' => round($participant->average_score, 2),
                    'last_attempt' => $participant->last_attempt,
                ];
            });

        return Inertia::render('Admin/Participants', [
            'participants' => $participants
        ]);
    }

    public function records()
    {
        if (!Session::get('admin_authenticated')) {
            return redirect()->route('admin.login');
        }

        return Inertia::render('Admin/Records', [
            'message' => 'Records management coming soon...'
        ]);
    }

    public function quizzes()
    {
        if (!Session::get('admin_authenticated')) {
            return redirect()->route('admin.login');
        }

        return Inertia::render('Admin/Quizzes', [
            'message' => 'Quiz management coming soon...'
        ]);
    }

    public function logout(Request $request)
    {
        Session::forget('admin_authenticated');
        $request->session()->regenerateToken();
        
        return redirect()->route('admin.login')->with([
            'message' => 'You have been logged out successfully.',
            'type' => 'success'
        ]);
    }

    public function dashboardData()
    {
        if (!Session::get('admin_authenticated')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            // Get basic stats
            $stats = [
                'total_participants' => DB::table('quiz_results')->distinct('participant_name')->count('participant_name'),
                'total_quiz_sets' => DB::table('quiz_sets')->where('is_active', 1)->count(),
                'total_attempts' => DB::table('quiz_results')->count(),
            ];

            // Quiz Set Distribution (Participants per quiz set)
            $quizSetDistribution = DB::table('quiz_results')
                ->join('quiz_sets', 'quiz_results.quiz_set_id', '=', 'quiz_sets.id')
                ->select(
                    'quiz_sets.id as quiz_set_id',
                    'quiz_sets.name as quiz_set_name',
                    'quiz_sets.category',
                    DB::raw('COUNT(DISTINCT quiz_results.participant_name) as participant_count')
                )
                ->where('quiz_sets.is_active', 1)
                ->groupBy('quiz_sets.id', 'quiz_sets.name', 'quiz_sets.category')
                ->orderBy('participant_count', 'desc')
                ->get();

            // Weekly Participants (Last 7 days)
            $startOfWeek = now()->subDays(6)->startOfDay();
            $endOfWeek = now()->endOfDay();
            
            $weeklyData = DB::table('quiz_results')
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(DISTINCT participant_name) as participant_count')
                )
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            // Generate labels for all days of the week
            $weeklyParticipants = [
                'labels' => [],
                'data' => []
            ];

            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $dayName = now()->subDays($i)->format('D');
                $weeklyParticipants['labels'][] = $dayName;
                
                $dayData = $weeklyData->firstWhere('date', $date);
                $weeklyParticipants['data'][] = $dayData ? $dayData->participant_count : 0;
            }

            // Top Scorers This Week (per quiz set)
            $topScorers = [];
            $activeQuizSets = DB::table('quiz_sets')->where('is_active', 1)->get();

            foreach ($activeQuizSets as $quizSet) {
                $topParticipants = DB::table('quiz_results')
                    ->select(
                        'participant_name',
                        'score',
                        'total_questions',
                        'percentage',
                        'created_at'
                    )
                    ->where('quiz_set_id', $quizSet->id)
                    ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                    ->orderBy('percentage', 'desc')
                    ->orderBy('score', 'desc')
                    ->orderBy('time_taken', 'asc')
                    ->limit(3)
                    ->get();

                $totalAttempts = DB::table('quiz_results')
                    ->where('quiz_set_id', $quizSet->id)
                    ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                    ->count();

                if ($topParticipants->count() > 0) {
                    $topScorers[] = [
                        'quiz_set_id' => $quizSet->id,
                        'quiz_set_name' => $quizSet->name,
                        'category' => $quizSet->category,
                        'total_attempts' => $totalAttempts,
                        'top_participants' => $topParticipants->map(function ($result) {
                            return [
                                'participant_name' => $result->participant_name,
                                'score' => $result->score,
                                'total_questions' => $result->total_questions,
                                'percentage' => $result->percentage,
                                'created_at' => $result->created_at,
                            ];
                        })->values()
                    ];
                }
            }

            // Recent Quiz Attempts (Last 10 attempts)
            $recentAttempts = DB::table('quiz_results')
                ->join('quiz_sets', 'quiz_results.quiz_set_id', '=', 'quiz_sets.id')
                ->select(
                    'quiz_results.id',
                    'quiz_results.participant_name',
                    'quiz_sets.name as quiz_set_name',
                    'quiz_sets.category',
                    'quiz_results.score',
                    'quiz_results.total_questions',
                    'quiz_results.percentage',
                    'quiz_results.time_taken',
                    'quiz_results.created_at'
                )
                ->where('quiz_sets.is_active', 1)
                ->orderBy('quiz_results.created_at', 'desc')
                ->limit(10)
                ->get();

            return response()->json([
                'stats' => $stats,
                'quizSetDistribution' => $quizSetDistribution,
                'weeklyParticipants' => $weeklyParticipants,
                'topScorers' => $topScorers,
                'recentAttempts' => $recentAttempts,
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching dashboard data: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch dashboard data'], 500);
        }
    }

    /**
     * Get recent quiz attempts from database
     */
    private function getRecentAttempts()
    {
        return DB::table('quiz_results')
            ->join('quiz_sets', 'quiz_results.quiz_set_id', '=', 'quiz_sets.id')
            ->select(
                'quiz_results.id',
                'quiz_results.participant_name',
                'quiz_sets.name as quiz_set_name',
                'quiz_results.score',
                'quiz_results.total_questions',
                'quiz_results.percentage',
                'quiz_results.created_at'
            )
            ->where('quiz_sets.is_active', 1)
            ->orderBy('quiz_results.created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($attempt) {
                return [
                    'id' => $attempt->id,
                    'participant_name' => $attempt->participant_name,
                    'quiz_set' => ['name' => $attempt->quiz_set_name],
                    'score' => $attempt->score,
                    'total_questions' => $attempt->total_questions,
                    'percentage' => $attempt->percentage,
                    'created_at' => $attempt->created_at,
                ];
            })
            ->toArray();
    }
}