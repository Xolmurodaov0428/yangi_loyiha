<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Supervisor
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user || $user->role !== 'supervisor') {
            abort(403, 'Ushbu sahifaga kirish uchun amaliyot rahbari bo\'lishingiz kerak.');
        }
        return $next($request);
    }
}
