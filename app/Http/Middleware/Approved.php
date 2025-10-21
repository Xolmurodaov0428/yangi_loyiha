<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Approved
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user || !$user->approved_at || !$user->is_active) {
            return redirect()->route('login')->withErrors(['email' => 'Hisobingiz hali tasdiqlanmagan yoki faol emas.']);
        }
        return $next($request);
    }
}
