<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class BaseAdminController extends Controller
{
    /**
     * Get admin profile data
     */
    protected function getAdminProfileData()
    {
        if (!Session::get('admin_authenticated')) {
            return null;
        }

        $adminEmail = Session::get('admin_email');
        $adminUser = DB::table('users')->where('email', $adminEmail)->first();

        if ($adminUser) {
            return [
                'firstName' => $adminUser->first_name ?? 'Admin',
                'lastName' => $adminUser->last_name ?? 'User',
                'username' => $adminUser->username ?? 'admin',
                'email' => $adminUser->email ?? $adminEmail,
                'phone' => $adminUser->phone ?? '',
                'bio' => $adminUser->bio ?? '',
                'avatar' => $adminUser->avatar ? Storage::url($adminUser->avatar) : null,
                'role' => $adminUser->role ?? 'admin',
                'createdAt' => $adminUser->created_at ? Carbon::parse($adminUser->created_at)->format('M j, Y') : 'Unknown',
                'lastLogin' => $adminUser->last_login_at ? Carbon::parse($adminUser->last_login_at)->diffForHumans() : 'Never',
            ];
        }

        // Fallback for when no admin user exists in database
        return [
            'firstName' => 'Admin',
            'lastName' => 'User',
            'username' => 'admin',
            'email' => $adminEmail,
            'phone' => '',
            'bio' => 'System Administrator',
            'avatar' => null,
            'role' => 'admin',
            'createdAt' => 'Unknown',
            'lastLogin' => 'Never',
        ];
    }

    /**
     * Get theme customization data
     * Checks for preview theme first, then falls back to saved theme
     */
    protected function getThemeData()
    {
        // Check for preview theme first (for real-time preview), then fall back to saved theme
        return [
            'colorScheme' => Cache::get('theme_preview.color_scheme', Cache::get('customization.color_scheme', 'light')),
            'primaryColor' => Cache::get('theme_preview.primary_color', Cache::get('customization.primary_color', 'blue')),
            'layout' => Cache::get('customization.layout', 'sidebar'),
        ];
    }

    /**
     * Get primary color values for CSS
     */
    protected function getPrimaryColorValues($colorId = null)
    {
        $colorId = $colorId ?: Cache::get('theme_preview.primary_color', Cache::get('customization.primary_color', 'blue'));
        
        $colors = [
            'blue' => [
                'primary' => '#3b82f6',
                'light' => '#dbeafe',
                'dark' => '#1e40af',
                'name' => 'Blue'
            ],
            'green' => [
                'primary' => '#10b981',
                'light' => '#dcfce7',
                'dark' => '#047857',
                'name' => 'Green'
            ],
            'purple' => [
                'primary' => '#8b5cf6',
                'light' => '#f3e8ff',
                'dark' => '#6d28d9',
                'name' => 'Purple'
            ],
            'red' => [
                'primary' => '#ef4444',
                'light' => '#fee2e2',
                'dark' => '#b91c1c',
                'name' => 'Red'
            ],
            'orange' => [
                'primary' => '#f59e0b',
                'light' => '#fef3c7',
                'dark' => '#d97706',
                'name' => 'Orange'
            ]
        ];

        return $colors[$colorId] ?? $colors['blue'];
    }

    /**
     * Apply theme preview (for real-time changes without saving)
     */
    protected function applyThemePreview($colorScheme, $primaryColor)
    {
        Cache::put('theme_preview.color_scheme', $colorScheme, 300); // 5 minutes
        Cache::put('theme_preview.primary_color', $primaryColor, 300);
    }

    /**
     * Clear theme preview (revert to saved theme)
     */
    protected function clearThemePreview()
    {
        Cache::forget('theme_preview.color_scheme');
        Cache::forget('theme_preview.primary_color');
    }

    /**
     * Check if theme preview is active
     */
    protected function isThemePreviewActive()
    {
        return Cache::has('theme_preview.color_scheme') || Cache::has('theme_preview.primary_color');
    }

    /**
     * Get all available color schemes
     */
    protected function getAvailableColorSchemes()
    {
        return [
            [
                'id' => 'light',
                'name' => 'Light',
                'description' => 'Clean and bright interface',
            ],
            [
                'id' => 'dark', 
                'name' => 'Dark',
                'description' => 'Easy on the eyes in low light',
            ],
            [
                'id' => 'auto',
                'name' => 'Auto',
                'description' => 'Adapts to system preference',
            ]
        ];
    }

    /**
     * Get all available primary colors
     */
    protected function getAvailablePrimaryColors()
    {
        return [
            ['id' => 'blue', 'name' => 'Blue', 'value' => '#3b82f6'],
            ['id' => 'green', 'name' => 'Green', 'value' => '#10b981'],
            ['id' => 'purple', 'name' => 'Purple', 'value' => '#8b5cf6'],
            ['id' => 'red', 'name' => 'Red', 'value' => '#ef4444'],
            ['id' => 'orange', 'name' => 'Orange', 'value' => '#f59e0b']
        ];
    }

    /**
     * Check admin authentication
     */
    protected function checkAdminAuth()
    {
        if (!Session::get('admin_authenticated')) {
            return redirect()->route('admin.login');
        }
        return null;
    }

    /**
     * Get dashboard statistics (common method for all dashboard-related controllers)
     */
    protected function getDashboardStats()
    {
        return [
            'totalParticipants' => DB::table('quiz_results')->distinct('participant_name')->count('participant_name'),
            'totalQuizSets' => DB::table('quiz_sets')->where('is_active', 1)->count(),
            'totalAttempts' => DB::table('quiz_results')->count(),
            'totalQuestions' => DB::table('quizzes')->count(),
            'averageScore' => round(DB::table('quiz_results')->avg('percentage') ?? 0, 2),
        ];
    }

    /**
     * Get recent activities for dashboard
     */
    protected function getRecentActivities($limit = 10)
    {
        return DB::table('quiz_results')
            ->join('quiz_sets', 'quiz_results.quiz_set_id', '=', 'quiz_sets.id')
            ->select(
                'quiz_results.id',
                'quiz_results.participant_name',
                'quiz_sets.name as quiz_set_name',
                'quiz_results.percentage',
                'quiz_results.created_at'
            )
            ->orderBy('quiz_results.created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($result) {
                return [
                    'id' => $result->id,
                    'type' => 'quiz_completed',
                    'message' => $result->participant_name . ' completed ' . $result->quiz_set_name . ' with ' . round($result->percentage) . '% score',
                    'created_at' => $result->created_at,
                ];
            });
    }
}