<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckImpersonation
{
    /**
     * Share impersonation status with all views.
     */
    public function handle(Request $request, Closure $next)
    {
        if (session()->has('impersonating')) {
            view()->share('impersonating', true);
            view()->share('impersonator_name', session('impersonator_name'));
        } else {
            view()->share('impersonating', false);
            view()->share('impersonator_name', null);
        }

        return $next($request);
    }
}
