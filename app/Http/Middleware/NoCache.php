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

        // Use the HeaderBag so this works for both Laravel responses and raw
        // Symfony StreamedResponse instances (e.g. file downloads).
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }
}
