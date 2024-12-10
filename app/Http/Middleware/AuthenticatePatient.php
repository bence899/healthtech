<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthenticatePatient
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->role !== 'patient') {
            abort(403);
        }

        return $next($request);
    }
} 