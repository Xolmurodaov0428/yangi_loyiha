<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckSupervisorSession
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role === 'supervisor') {
            // Ensure session is properly configured for supervisor
            config(['session.lifetime' => 120]); // 2 hours
            config(['session.expire_on_close' => false]);
        }

        return $next($request);
    }
}
