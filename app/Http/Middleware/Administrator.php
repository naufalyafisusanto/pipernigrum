<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Administrator
{
    /**
     * Administrator.
     *
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->user()->admin)
        {
            abort(403);
        }
        
        return $next($request);
    }
}
