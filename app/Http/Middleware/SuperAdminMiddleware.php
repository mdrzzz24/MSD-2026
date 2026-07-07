<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || !Auth::user()->is_admin || Auth::user()->role !== 'super_admin') {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Access denied. Super admin only.');
        }

        return $next($request);
    }
}
