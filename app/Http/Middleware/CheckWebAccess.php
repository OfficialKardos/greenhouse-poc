<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckWebAccess
{
    public function handle(Request $request, Closure $next)
    {
        // If user is not logged in, allow access to login page
        if (!auth()->check()) {
            // Allow access to login page
            if ($request->path() === 'login') {
                return $next($request);
            }
            
            // For API/json requests
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.'
                ], 401);
            }
            
            // For web requests, redirect to login
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Check if user has admin panel access
        if (!$user->can_access_admin) {
            // Log the user out
            auth()->logout();
            
            // Clear session
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            // For API/json requests
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have access to the admin panel.'
                ], 403);
            }
            
            // For web requests, redirect to login with error
            return redirect()->route('login')->with('error', 'You do not have access to the admin panel.');
        }

        return $next($request);
    }
}