<?php
// app/Http/Middleware/MobileAuth.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MobileAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('mobile_worker_token') && !$request->has('token')) {
            return redirect()->route('mobile.login');
        }

        return $next($request);
    }
}