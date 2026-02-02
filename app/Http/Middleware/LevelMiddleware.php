<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LevelMiddleware
{
    public function handle($request, Closure $next, ...$levels)
    {
        if (! auth()->check()) {
            abort(403);
        }

        if (! in_array(auth()->user()->id_level, $levels)) {
            abort(403);
        }


        return $next($request);
    }
}
