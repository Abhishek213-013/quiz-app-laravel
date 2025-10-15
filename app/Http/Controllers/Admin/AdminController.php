<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AdminController extends BaseAdminController
{
    private $adminCredentials = [
        'email' => 'admin@quiz.com',
        'password' => 'admin123' // You should hash this in production
    ];

    /**
     * =========================================================================
     * AUTHENTICATION METHODS
     * =========================================================================
     */

    public function login()
    {
        if (Session::get('admin_authenticated')) {
            return redirect()->route('admin.dashboard');
        }
        
        return Inertia::render('Admin/Login');
    }

    // Fixed authenticate method with proper error handling
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Check if using simple session authentication or Eloquent authentication
        if ($request->email === $this->adminCredentials['email'] && 
            $request->password === $this->adminCredentials['password']) {
            
            // Simple session-based authentication
            Session::put('admin_authenticated', true);
            Session::put('admin_email', $request->email);
            $request->session()->regenerate();
            
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        // Logout from Auth if used
        if (Auth::check()) {
            Auth::logout();
        }
        
        Session::forget('admin_authenticated');
        Session::forget('admin_email');
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('admin.login')->with([
            'message' => 'You have been logged out successfully.',
            'type' => 'success'
        ]);
    }

    public function verify(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => 'Invalid input'], 422);
            }

            if ($request->email === $this->adminCredentials['email'] && 
                $request->password === $this->adminCredentials['password']) {
                return response()->json(['authenticated' => true]);
            }

            return response()->json(['error' => 'Invalid credentials'], 401);

        } catch (\Exception $e) {
            Log::error('Admin verification error: ' . $e->getMessage());
            return response()->json(['error' => 'Verification failed'], 500);
        }
    }

    /**
     * =========================================================================
     * PAGE RENDERING METHODS
     * =========================================================================
     */

    public function dashboard()
    {
        if ($redirect = $this->checkAdminAuth()) {
            return $redirect;
        }

        $stats = [
            'totalParticipants' => DB::table('quiz_results')->distinct('participant_name')->count('participant_name'),
            'totalQuizSets' => DB::table('quiz_sets')->where('is_active', 1)->count(),
            'totalAttempts' => DB::table('quiz_results')->count(),
        ];

        return Inertia::render('Admin/Dashboard', [
            'profile' => $this->getAdminProfileData(),
            'theme' => $this->getThemeData(), // Add theme data
            'initialStats' => $stats
        ]);
    }

    public function participants()
    {
        if ($redirect = $this->checkAdminAuth()) {
            return $redirect;
        }

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
                    'best_score' => $participant->best_score ?? 0,
                    'best_percentage' => round($participant->best_percentage ?? 0, 2),
                    'total_attempts' => $participant->total_attempts ?? 0,
                    'average_score' => round($participant->average_score ?? 0, 2),
                    'last_attempt' => $participant->last_attempt,
                ];
            });

        return Inertia::render('Admin/Participants', [
            'profile' => $this->getAdminProfileData(),
            'theme' => $this->getThemeData(), // Add theme data
            'participants' => $participants
        ]);
    }

    public function records()
    {
        if ($redirect = $this->checkAdminAuth()) {
            return $redirect;
        }

        return Inertia::render('Admin/Records', [
            'profile' => $this->getAdminProfileData(),
            'theme' => $this->getThemeData() // Add theme data
        ]);
    }

    public function quizzes()
    {
        if ($redirect = $this->checkAdminAuth()) {
            return $redirect;
        }

        return Inertia::render('Admin/Quizzes', [
            'profile' => $this->getAdminProfileData(),
            'theme' => $this->getThemeData() // Add theme data
        ]);
    }

    /**
     * =========================================================================
     * DASHBOARD DATA METHODS
     * =========================================================================
     */

    public function getDashboardStats()
    {
        try {
            $stats = [
                'total_participants' => DB::table('quiz_results')->distinct('participant_name')->count('participant_name'),
                'total_quiz_sets' => DB::table('quiz_sets')->where('is_active', 1)->count(),
                'total_attempts' => DB::table('quiz_results')->count(),
                'total_questions' => DB::table('quizzes')->count(),
                'average_score' => round(DB::table('quiz_results')->avg('percentage') ?? 0, 2),
            ];

            return response()->json($stats);

        } catch (\Exception $e) {
            Log::error('Error fetching dashboard stats: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch dashboard stats'], 500);
        }
    }

    public function dashboardData()
    {
        try {
            // Basic stats
            $stats = [
                'total_participants' => DB::table('quiz_results')->distinct('participant_name')->count('participant_name'),
                'total_quiz_sets' => DB::table('quiz_sets')->where('is_active', 1)->count(),
                'total_attempts' => DB::table('quiz_results')->count(),
                'average_score' => round(DB::table('quiz_results')->avg('percentage') ?? 0, 2),
            ];

            // Quiz set distribution
            $quizSetDistribution = DB::table('quiz_results')
                ->join('quiz_sets', 'quiz_results.quiz_set_id', '=', 'quiz_sets.id')
                ->select(
                    'quiz_sets.id',
                    'quiz_sets.name as quiz_set_name',
                    'quiz_sets.category',
                    DB::raw('COUNT(DISTINCT quiz_results.participant_name) as participant_count')
                )
                ->where('quiz_sets.is_active', 1)
                ->groupBy('quiz_sets.id', 'quiz_sets.name', 'quiz_sets.category')
                ->orderBy('participant_count', 'desc')
                ->get();

            // Weekly participants data
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

            // Top scorers for each quiz set
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
                        })
                    ];
                }
            }

            // Recent attempts
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
                    'quiz_results.created_at'
                )
                ->where('quiz_sets.is_active', 1)
                ->orderBy('quiz_results.created_at', 'desc')
                ->limit(10)
                ->get();

            // Score distribution
            $scoreDistribution = [
                'high' => DB::table('quiz_results')->where('percentage', '>=', 80)->count(),
                'medium' => DB::table('quiz_results')->whereBetween('percentage', [60, 79])->count(),
                'low' => DB::table('quiz_results')->where('percentage', '<', 60)->count(),
            ];

            // Recent activities
            $recentActivities = DB::table('quiz_results')
                ->join('quiz_sets', 'quiz_results.quiz_set_id', '=', 'quiz_sets.id')
                ->select(
                    'quiz_results.id',
                    'quiz_results.participant_name',
                    'quiz_sets.name as quiz_set_name',
                    'quiz_results.percentage',
                    'quiz_results.created_at'
                )
                ->orderBy('quiz_results.created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($result) {
                    return [
                        'id' => $result->id,
                        'type' => 'quiz_completed',
                        'message' => $result->participant_name . ' completed ' . $result->quiz_set_name . ' with ' . round($result->percentage) . '% score',
                        'created_at' => $result->created_at,
                    ];
                });

            return response()->json([
                'stats' => $stats,
                'quiz_set_distribution' => $quizSetDistribution,
                'weekly_participants' => $weeklyParticipants,
                'top_scorers' => $topScorers,
                'recent_attempts' => $recentAttempts,
                'score_distribution' => $scoreDistribution,
                'recent_activities' => $recentActivities,
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching dashboard data: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch dashboard data'], 500);
        }
    }

    /**
     * =========================================================================
     * PARTICIPANTS DATA METHODS
     * =========================================================================
     */

    public function getParticipants()
    {
        try {
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
                ->get();

            return response()->json($participants);

        } catch (\Exception $e) {
            Log::error('Error fetching participants: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch participants'], 500);
        }
    }

    public function getParticipantDetail($name)
    {
        try {
            $participant = DB::table('quiz_results')
                ->join('quiz_sets', 'quiz_results.quiz_set_id', '=', 'quiz_sets.id')
                ->select(
                    'quiz_results.*',
                    'quiz_sets.name as quiz_set_name',
                    'quiz_sets.category'
                )
                ->where('participant_name', $name)
                ->orderBy('quiz_results.created_at', 'desc')
                ->get();

            return response()->json($participant);

        } catch (\Exception $e) {
            Log::error('Error fetching participant detail: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch participant detail'], 500);
        }
    }

    /**
     * =========================================================================
     * RECORDS DATA METHODS
     * =========================================================================
     */

    public function getAllRecords()
    {
        try {
            $records = DB::table('quiz_results')
                ->join('quiz_sets', 'quiz_results.quiz_set_id', '=', 'quiz_sets.id')
                ->select(
                    'quiz_results.*',
                    'quiz_sets.name as quiz_set_name',
                    'quiz_sets.category'
                )
                ->orderBy('quiz_results.created_at', 'desc')
                ->get();

            return response()->json($records);

        } catch (\Exception $e) {
            Log::error('Error fetching all records: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch records'], 500);
        }
    }

    public function getDailyParticipation()
    {
        try {
            $data = DB::table('quiz_results')
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(*) as attempts'),
                    DB::raw('COUNT(DISTINCT participant_name) as participants')
                )
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return response()->json($data);

        } catch (\Exception $e) {
            Log::error('Error fetching daily participation: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch daily participation'], 500);
        }
    }

    public function getRecordsData()
    {
        try {
            $records = DB::table('quiz_results')
                ->join('quiz_sets', 'quiz_results.quiz_set_id', '=', 'quiz_sets.id')
                ->select(
                    'quiz_results.id',
                    'quiz_results.quiz_set_id',
                    'quiz_results.participant_name',
                    'quiz_results.score',
                    'quiz_results.total_questions',
                    'quiz_results.percentage',
                    'quiz_results.time_taken',
                    'quiz_results.created_at',
                    'quiz_sets.name as quiz_set_name',
                    'quiz_sets.category'
                )
                ->orderBy('quiz_results.created_at', 'desc')
                ->get();

            return response()->json($records);

        } catch (\Exception $e) {
            Log::error('Error fetching records data: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch records data'], 500);
        }
    }

    public function recordsData(Request $request)
    {
        try {
            $records = DB::table('quiz_results')
                ->join('quiz_sets', 'quiz_results.quiz_set_id', '=', 'quiz_sets.id')
                ->select(
                    'quiz_results.*',
                    'quiz_sets.name as quiz_set_name',
                    'quiz_sets.category'
                )
                ->where('quiz_sets.is_active', 1)
                ->orderBy('quiz_results.created_at', 'desc')
                ->get();

            // Overall stats
            $overallStats = [
                'highestScore' => $records->max('percentage'),
                'averageScore' => round($records->avg('percentage'), 2),
                'totalParticipants' => $records->unique('participant_name')->count(),
                'totalAttempts' => $records->count()
            ];

            // Quiz sets for filtering
            $quizSets = DB::table('quiz_sets')
                ->where('is_active', 1)
                ->select('id', 'name', 'category')
                ->get();

            // Top performers (all time)
            $topPerformers = $records
                ->sortByDesc('percentage')
                ->take(6)
                ->values();

            // Weekly leaderboard
            $oneWeekAgo = now()->subDays(7);
            $weeklyLeaderboard = $records
                ->where('created_at', '>=', $oneWeekAgo)
                ->sortByDesc('percentage')
                ->take(10)
                ->values();

            // Quiz set performance
            $quizSetPerformance = [];
            $quizSets->each(function ($set) use ($records, &$quizSetPerformance) {
                $setRecords = $records->where('quiz_set_id', $set->id);
                if ($setRecords->isNotEmpty()) {
                    $highest = $setRecords->sortByDesc('percentage')->first();
                    $lowest = $setRecords->sortBy('percentage')->first();
                    
                    $quizSetPerformance['highest'][] = [
                        'quiz_set_id' => $set->id,
                        'quiz_set_name' => $set->name,
                        'category' => $set->category,
                        'highest_percentage' => $highest->percentage,
                        'top_performer' => $highest->participant_name
                    ];
                    
                    $quizSetPerformance['lowest'][] = [
                        'quiz_set_id' => $set->id,
                        'quiz_set_name' => $set->name,
                        'category' => $set->category,
                        'lowest_percentage' => $lowest->percentage,
                        'lowest_performer' => $lowest->participant_name
                    ];
                }
            });

            // Sort quiz set performance
            if (isset($quizSetPerformance['highest'])) {
                $quizSetPerformance['highest'] = collect($quizSetPerformance['highest'])
                    ->sortByDesc('highest_percentage')
                    ->values()
                    ->toArray();
            }
            
            if (isset($quizSetPerformance['lowest'])) {
                $quizSetPerformance['lowest'] = collect($quizSetPerformance['lowest'])
                    ->sortBy('lowest_percentage')
                    ->values()
                    ->toArray();
            }

            return response()->json([
                'overallStats' => $overallStats,
                'topPerformers' => [
                    'allTime' => $topPerformers
                ],
                'weeklyLeaderboard' => $weeklyLeaderboard,
                'quizSetPerformance' => $quizSetPerformance,
                'allRecords' => $records,
                'quizSets' => $quizSets
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching records data: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch records data'], 500);
        }
    }

    /**
     * =========================================================================
     * QUIZ SETS MANAGEMENT METHODS
     * =========================================================================
     */

    public function getQuizSets()
    {
        try {
            Log::info('Fetching quiz sets from database');
            
            $quizSets = DB::table('quiz_sets')
                ->select('*')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($quizSet) {
                    $questionCount = DB::table('quizzes')
                        ->where('quiz_set_id', $quizSet->id)
                        ->count();
                    
                    return [
                        'id' => $quizSet->id,
                        'name' => $quizSet->name,
                        'category' => $quizSet->category,
                        'description' => $quizSet->description,
                        'time_limit' => $quizSet->time_limit,
                        'question_count' => $questionCount,
                        'is_active' => $quizSet->is_active,
                        'created_at' => $quizSet->created_at,
                        'updated_at' => $quizSet->updated_at,
                    ];
                });

            Log::info('Found ' . $quizSets->count() . ' quiz sets');
            return response()->json($quizSets);

        } catch (\Exception $e) {
            Log::error('Error fetching quiz sets: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch quiz sets'], 500);
        }
    }

    public function storeQuizSet(Request $request)
    {
        Log::info('=== STORE QUIZ SET START ===');
        Log::info('Request data:', $request->all());

        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'category' => 'required|string|max:255',
                'description' => 'required|string',
                'time_limit' => 'required|integer|min:1',
                'question_count' => 'required|integer|min:1'
            ]);

            if ($validator->fails()) {
                Log::error('Validation failed:', $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            Log::info('Validation passed, inserting into database...');

            $quizSetId = DB::table('quiz_sets')->insertGetId([
                'name' => $request->name,
                'category' => $request->category,
                'description' => $request->description,
                'time_limit' => $request->time_limit,
                'question_count' => $request->question_count,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::info('Quiz set created successfully with ID: ' . $quizSetId);

            return response()->json([
                'success' => true,
                'id' => $quizSetId, 
                'message' => 'Quiz set created successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error creating quiz set: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'error' => 'Failed to create quiz set: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateQuizSet(Request $request, $id)
    {
        Log::info('=== UPDATE QUIZ SET START ===');
        Log::info('Updating quiz set ID: ' . $id);
        Log::info('Request data:', $request->all());

        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'category' => 'required|string|max:255',
                'description' => 'required|string',
                'time_limit' => 'required|integer|min:1',
                'question_count' => 'required|integer|min:1'
            ]);

            if ($validator->fails()) {
                Log::error('Validation failed:', $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $updated = DB::table('quiz_sets')
                ->where('id', $id)
                ->update([
                    'name' => $request->name,
                    'category' => $request->category,
                    'description' => $request->description,
                    'time_limit' => $request->time_limit,
                    'question_count' => $request->question_count,
                    'updated_at' => now(),
                ]);

            if ($updated) {
                Log::info('Quiz set updated successfully');
                return response()->json([
                    'success' => true,
                    'message' => 'Quiz set updated successfully'
                ]);
            }

            Log::error('Quiz set not found with ID: ' . $id);
            return response()->json([
                'success' => false,
                'error' => 'Quiz set not found'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Error updating quiz set: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'error' => 'Failed to update quiz set: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteQuizSet($id)
    {
        Log::info('=== DELETE QUIZ SET START ===');
        Log::info('Deleting quiz set ID: ' . $id);

        try {
            DB::table('quizzes')->where('quiz_set_id', $id)->delete();
            $deleted = DB::table('quiz_sets')->where('id', $id)->delete();

            if ($deleted) {
                Log::info('Quiz set deleted successfully');
                return response()->json([
                    'success' => true,
                    'message' => 'Quiz set deleted successfully'
                ]);
            }

            Log::error('Quiz set not found with ID: ' . $id);
            return response()->json([
                'success' => false,
                'error' => 'Quiz set not found'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Error deleting quiz set: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'error' => 'Failed to delete quiz set: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * =========================================================================
     * QUIZZES MANAGEMENT METHODS
     * =========================================================================
     */

    public function getQuizzes($quizSetId = null)
    {
        try {
            $query = DB::table('quizzes')
                ->join('quiz_sets', 'quizzes.quiz_set_id', '=', 'quiz_sets.id')
                ->select(
                    'quizzes.*',
                    'quiz_sets.name as quiz_set_name'
                );

            if ($quizSetId) {
                $query->where('quizzes.quiz_set_id', $quizSetId);
            }

            $quizzes = $query->orderBy('quizzes.created_at', 'desc')->get();

            return response()->json($quizzes);

        } catch (\Exception $e) {
            Log::error('Error fetching quizzes: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch quizzes'], 500);
        }
    }

    public function storeQuiz(Request $request)
    {
        Log::info('=== STORE QUIZ START ===');
        Log::info('Request data:', $request->all());

        try {
            $validator = Validator::make($request->all(), [
                'quiz_set_id' => 'required|integer|exists:quiz_sets,id',
                'question' => 'required|string',
                'question_type' => 'required|in:multiple_choice,brief_answer,true_false',
                'correct_answer' => 'required|string',
                'points' => 'required|integer|min:1'
            ]);

            if ($validator->fails()) {
                Log::error('Validation failed:', $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $quizData = [
                'quiz_set_id' => $request->quiz_set_id,
                'question' => $request->question,
                'question_type' => $request->question_type,
                'correct_answer' => $request->correct_answer,
                'points' => $request->points,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if ($request->question_type === 'multiple_choice') {
                if (!isset($request->options) || !is_array($request->options)) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['options' => ['Options are required for multiple choice questions']]
                    ], 422);
                }
                $quizData['options'] = json_encode($request->options);
            } else {
                $quizData['options'] = null;
            }

            Log::info('Inserting quiz data:', $quizData);

            $quizId = DB::table('quizzes')->insertGetId($quizData);

            Log::info('Quiz created successfully with ID: ' . $quizId);

            return response()->json([
                'success' => true,
                'id' => $quizId, 
                'message' => 'Question created successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error creating question: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'error' => 'Failed to create question: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateQuiz(Request $request, $id)
    {
        Log::info('=== UPDATE QUIZ START ===');
        Log::info('Updating quiz ID: ' . $id);
        Log::info('Request data:', $request->all());

        try {
            $validator = Validator::make($request->all(), [
                'question' => 'required|string',
                'question_type' => 'required|in:multiple_choice,brief_answer,true_false',
                'correct_answer' => 'required|string',
                'points' => 'required|integer|min:1'
            ]);

            if ($validator->fails()) {
                Log::error('Validation failed:', $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $quizData = [
                'question' => $request->question,
                'question_type' => $request->question_type,
                'correct_answer' => $request->correct_answer,
                'points' => $request->points,
                'updated_at' => now(),
            ];

            if ($request->question_type === 'multiple_choice') {
                if (!isset($request->options) || !is_array($request->options)) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['options' => ['Options are required for multiple choice questions']]
                    ], 422);
                }
                $quizData['options'] = json_encode($request->options);
            } else {
                $quizData['options'] = null;
            }

            $updated = DB::table('quizzes')
                ->where('id', $id)
                ->update($quizData);

            if ($updated) {
                Log::info('Quiz updated successfully');
                return response()->json([
                    'success' => true,
                    'message' => 'Question updated successfully'
                ]);
            }

            Log::error('Quiz not found with ID: ' . $id);
            return response()->json([
                'success' => false,
                'error' => 'Question not found'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Error updating question: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'error' => 'Failed to update question: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteQuiz($id)
    {
        Log::info('=== DELETE QUIZ START ===');
        Log::info('Deleting quiz ID: ' . $id);

        try {
            $deleted = DB::table('quizzes')->where('id', $id)->delete();

            if ($deleted) {
                Log::info('Quiz deleted successfully');
                return response()->json([
                    'success' => true,
                    'message' => 'Question deleted successfully'
                ]);
            }

            Log::error('Quiz not found with ID: ' . $id);
            return response()->json([
                'success' => false,
                'error' => 'Question not found'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Error deleting question: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'error' => 'Failed to delete question: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * =========================================================================
     * NOTIFICATION METHODS
     * =========================================================================
     */

    /**
 * =========================================================================
 * NOTIFICATION METHODS - UPDATED
 * =========================================================================
 */

    public function getNotifications(Request $request)
    {
        try {
            // Get recent quiz attempts for notifications
            $recentAttempts = DB::table('quiz_results')
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
                ->where('quiz_results.created_at', '>=', now()->subHours(24))
                ->orderBy('quiz_results.created_at', 'desc')
                ->limit(20)
                ->get()
                ->map(function ($attempt) use ($request) {
                    // Check if this notification has been marked as read in session
                    $readNotifications = $request->session()->get('read_notifications', []);
                    $isRead = in_array($attempt->id, $readNotifications);
                    
                    return [
                        'id' => $attempt->id,
                        'type' => 'quiz_attempt',
                        'message' => $attempt->participant_name . ' attempted ' . $attempt->quiz_set_name . ' with ' . round($attempt->percentage) . '% score',
                        'created_at' => $attempt->created_at,
                        'read' => $isRead
                    ];
                });

            // Filter out read notifications if you want to hide them completely
            $unreadNotifications = $recentAttempts->filter(function ($notification) {
                return !$notification['read'];
            });

            return response()->json([
                'notifications' => $unreadNotifications->values()
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching notifications: ' . $e->getMessage());
            return response()->json(['notifications' => []]);
        }
    }

    public function markNotificationAsRead(Request $request, $id)
    {
        try {
            // Get current read notifications from session
            $readNotifications = $request->session()->get('read_notifications', []);
            
            // Add this notification ID to read list
            if (!in_array($id, $readNotifications)) {
                $readNotifications[] = $id;
                $request->session()->put('read_notifications', $readNotifications);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Error marking notification as read: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to mark notification as read'], 500);
        }
    }

    public function markAllNotificationsAsRead(Request $request)
    {
        try {
            // Get all recent notification IDs
            $recentAttempts = DB::table('quiz_results')
                ->where('created_at', '>=', now()->subHours(24))
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->pluck('id')
                ->toArray();

            // Store all recent notification IDs as read in session
            $request->session()->put('read_notifications', $recentAttempts);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Error marking all notifications as read: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to mark all notifications as read'], 500);
        }
    }
}