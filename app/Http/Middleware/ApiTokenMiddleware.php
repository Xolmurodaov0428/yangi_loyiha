<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ApiToken;

class ApiTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'API token talab qilinadi',
            ], 401);
        }

        $apiToken = ApiToken::where('token', $token)->first();

        if (!$apiToken) {
            return response()->json([
                'success' => false,
                'message' => 'Noto\'g\'ri API token',
            ], 401);
        }

        if (!$apiToken->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'API token yaroqsiz yoki muddati tugagan',
            ], 401);
        }

        // Mark token as used
        $apiToken->markAsUsed();

        return $next($request);
    }
}
