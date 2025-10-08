<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminAuth
{
    public function handle(Request $request, Closure $next)
    {
        $secretKey = $request->header('X-Admin-Key');
        
        // You can also get the secret key from the request if needed
        if (!$secretKey) {
            $secretKey = $request->input('secret_key');
        }
        
        if ($secretKey !== 'admin123') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}