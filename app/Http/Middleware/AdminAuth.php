<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminAuth
{
    public function handle(Request $request, Closure $next)
    {
        $secretKey = $request->header('X-Admin-Key');
        
        if ($secretKey !== 'admin123') { // Match this with your secret key
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}