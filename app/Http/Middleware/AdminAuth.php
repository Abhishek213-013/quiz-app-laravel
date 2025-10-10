<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if admin is authenticated via session
        if (!Session::get('admin_authenticated')) {
            // If it's an API request, return JSON error
            if ($request->expectsJson() || $request->is('admin/dashboard-data')) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            
            // For page requests, redirect to login
            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}