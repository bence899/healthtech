<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthenticateDoctor
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->role !== 'doctor') {
            abort(403);
        }

        return $next($request);
    }
} 