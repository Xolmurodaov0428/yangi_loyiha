<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Admin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user || $user->role !== 'admin') {
            abort(403, 'Ushbu sahifaga kirish uchun admin bo\'lishingiz kerak.');
        }
        return $next($request);
    }
}
