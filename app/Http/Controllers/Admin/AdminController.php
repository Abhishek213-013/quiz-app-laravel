<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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
            $validator = Validator::make($request->all(), [
                'secret_key' => 'required|string|min:3'
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $adminSecretKey = config('app.admin_secret_key', 'admin123');
            
            if ($request->secret_key === $adminSecretKey) {
                Session::put('admin_authenticated', true);
                Session::save();
                
                return redirect()->route('admin.dashboard')->with([
                    'message' => 'Welcome to the admin panel!',
                    'type' => 'success'
                ]);
            }

            return back()->withErrors([
                'secret_key' => 'Invalid secret key. Please try again.'
            ])->withInput();

        } catch (\Exception $e) {
            Log::error('Admin authentication error: ' . $e->getMessage());
            return back()->withErrors([
                'secret_key' => 'Authentication failed. Please try again.'
            ])->withInput();
        }
    }

    public function dashboard()
    {
        // Get basic stats from database for initial render
        $stats = [
            'totalParticipants' => DB::table('quiz_results')->distinct('participant_name')->count('participant_name'),
            'totalQuizSets' => DB::table('quiz_sets')->where('is_active', 1)->count(),
            'totalAttempts' => DB::table('quiz_results')->count(),
        ];

        return Inertia::render('Admin/Dashboard', [
            'initialStats' => $stats
        ]);
    }

    public function participants()
    {
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
                    'best_score' => $participant->best_score ?? 0,
                    'best_percentage' => round($participant->best_percentage ?? 0, 2),
                    'total_attempts' => $participant->total_attempts ?? 0,
                    'average_score' => round($participant->average_score ?? 0, 2),
                    'last_attempt' => $participant->last_attempt,
                ];
            });

        return Inertia::render('Admin/Participants', [
            'participants' => $participants
        ]);
    }

    public function records()
    {
        return Inertia::render('Admin/Records');
    }

    public function quizzes()
    {
        return Inertia::render('Admin/Quizzes');
    }

    public function logout(Request $request)
    {
        Session::forget('admin_authenticated');
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('admin.login')->with([
            'message' => 'You have been logged out successfully.',
            'type' => 'success'
        ]);
    }
    public function getRecordsData()
    {
        // Check authentication
        if (!Session::get('admin_authenticated')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            // Get all quiz results with quiz set information
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
    public function dashboardData()
    {
        try {
            $stats = [
                'total_participants' => DB::table('quiz_results')->distinct('participant_name')->count('participant_name'),
                'total_quiz_sets' => DB::table('quiz_sets')->where('is_active', 1)->count(),
                'total_attempts' => DB::table('quiz_results')->count(),
            ];

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
                ->get()
                ->map(function ($attempt) {
                    return [
                        'id' => $attempt->id,
                        'participant_name' => $attempt->participant_name,
                        'quiz_set_name' => $attempt->quiz_set_name,
                        'category' => $attempt->category,
                        'score' => $attempt->score,
                        'total_questions' => $attempt->total_questions,
                        'percentage' => $attempt->percentage,
                        'created_at' => $attempt->created_at,
                    ];
                });

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

    // Admin API Methods for Quiz Management
    public function verify(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'secret_key' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => 'Invalid input'], 422);
            }

            $adminSecretKey = config('app.admin_secret_key', 'admin123');
            
            if ($request->secret_key === $adminSecretKey) {
                return response()->json(['authenticated' => true]);
            }

            return response()->json(['error' => 'Invalid secret key'], 401);

        } catch (\Exception $e) {
            Log::error('Admin verification error: ' . $e->getMessage());
            return response()->json(['error' => 'Verification failed'], 500);
        }
    }

    public function getDashboardStats()
    {
        try {
            $stats = [
                'total_participants' => DB::table('quiz_results')->distinct('participant_name')->count('participant_name'),
                'total_quiz_sets' => DB::table('quiz_sets')->where('is_active', 1)->count(),
                'total_attempts' => DB::table('quiz_results')->count(),
                'total_questions' => DB::table('quizzes')->count(),
            ];

            return response()->json($stats);

        } catch (\Exception $e) {
            Log::error('Error fetching dashboard stats: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch dashboard stats'], 500);
        }
    }

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

    public function getQuizSets()
    {
        try {
            Log::info('Fetching quiz sets from database');
            
            $quizSets = DB::table('quiz_sets')
                ->select('*')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($quizSet) {
                    // Count actual questions for this quiz set
                    $questionCount = DB::table('quizzes')
                        ->where('quiz_set_id', $quizSet->id)
                        ->count();
                    
                    return [
                        'id' => $quizSet->id,
                        'name' => $quizSet->name,
                        'category' => $quizSet->category,
                        'description' => $quizSet->description,
                        'time_limit' => $quizSet->time_limit,
                        'question_count' => $questionCount, // Use actual count
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
            // First delete all quizzes in this set
            DB::table('quizzes')->where('quiz_set_id', $id)->delete();
            
            // Then delete the quiz set
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

            // Only include options for multiple_choice questions
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

            // Only include options for multiple_choice questions
            if ($request->question_type === 'multiple_choice') {
                if (!isset($request->options) || !is_array($request->options)) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['options' => ['Options are required for multiple choice questions']]
                    ], 422);
                }
                $quizData['options'] = json_encode($request->options);
            } else {
                // For non-multiple choice, set options to null
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

    // Records Data API Method
   public function recordsData(Request $request)
    {
        try {
            // Get all records with quiz set information
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

            // Calculate overall stats
            $overallStats = [
                'highestScore' => $records->max('percentage'),
                'averageScore' => round($records->avg('percentage'), 2),
                'totalParticipants' => $records->unique('participant_name')->count(),
                'totalAttempts' => $records->count()
            ];

            // Get quiz sets for filtering
            $quizSets = DB::table('quiz_sets')
                ->where('is_active', 1)
                ->select('id', 'name', 'category')
                ->get();

            // Top performers (all time)
            $topPerformers = $records
                ->sortByDesc('percentage')
                ->take(6)
                ->values();

            // Weekly leaderboard (last 7 days)
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
}