<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceJsonResponse
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        /** Force the client to accept JSON */
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
