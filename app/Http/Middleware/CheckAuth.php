<?php

namespace App\Http\Middleware;

use App\Services\JWTManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Authorization');

        if (! $token) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $u = JWTManager::verifyToken($token);
        if (! $u) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
