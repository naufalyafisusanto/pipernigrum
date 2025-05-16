<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Arr;

class ForceRootUrl
{
    public function handle($request, Closure $next)
    {
        if (str_contains(Arr::get($_SERVER, 'HTTP_HOST', ''), 'naganoharamirai')) {
            URL::forceRootUrl(config('app.url'));
        }

        return $next($request);
    }
}
