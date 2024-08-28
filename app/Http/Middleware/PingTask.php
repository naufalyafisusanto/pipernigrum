<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Jobs\RunPingTask;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class PingTask
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $cacheKey = 'last_dispatch_timestamp';
        $currentTime = now();

        if (!Cache::has($cacheKey) || Cache::get($cacheKey)->diffInSeconds($currentTime) >= 3) {
            Cache::put($cacheKey, $currentTime, 3);

            RunPingTask::dispatch();
        }
        
        return $next($request);
    }
}
