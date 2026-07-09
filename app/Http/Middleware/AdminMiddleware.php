<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Access denied. Please login first.');
        }

        $user = Auth::user();

        // Allow admin users (is_admin = true) AND client users (role = 'client')
        if (!$user->is_admin && $user->role !== 'client') {
            return redirect()->route('login')->with('error', 'Access denied. Please login as admin.');
        }

        return $next($request);
    }
}
