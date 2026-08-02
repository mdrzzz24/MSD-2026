<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NoCache
{
    /**
     * Prevent the browser from caching admin pages so the newest content
     * (e.g. the realtime clock) is always shown without a manual refresh.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->header('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->header('Pragma', 'no-cache');
        $response->header('Expires', '0');

        return $response;
    }
}
