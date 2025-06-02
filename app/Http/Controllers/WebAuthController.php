<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class WebAuthController extends Controller
{
    public function register(Request $request, UserService $userService)
    {
        return $userService->register(
            $request['email'] ?? '',
            $request['password'] ?? ''
        );
    }

    public function login(Request $request, UserService $userService)
    {
        $tokens = $userService->login(
            $request['email'] ?? '',
            $request['password'] ?? ''
        );

        return response()->json(["message" => "success"])->withCookie(
            cookie(
                'access_token',
                $tokens["access_token"],
                $minutes = config("auth.refresh_token_lifetime") / 60,
                $path = '/',
                $domain = config('app.debug') ? null : 'localhost',
                $secure = !config("app.debug"),
                $httpOnly = false,
                true,
                "lax"
            )
        )->withCookie(
                cookie(
                    'refresh_token',
                    $tokens["refresh_token"],
                    $minutes = config("auth.refresh_token_lifetime") / 60,
                    $path = '/',
                    $domain = config('app.debug') ? null : 'localhost',
                    $secure = !config("app.debug"),
                    $httpOnly = true,
                    true,
                    $sameSite = 'lax'
                )
            );
    }

    public function auth(Request $request, UserService $userService)
    {
        return $userService->auth($request->bearerToken() ?? '');
    }

    public function refresh(Request $request, UserService $userService)
    {


        $tokens = $userService->refresh_access_token($request->cookie('refresh_token') ?? '');

        Cookie::queue(
            'access_token',
            $tokens["access_token"],
            $minutes = config("auth.refresh_token_lifetime") / 60,
            $path = '/',
            $domain = config('app.debug') ? null : 'localhost',
            $secure = config("app.debug"),
            $httpOnly = false,
            $sameSite = 'lax'
        );
        return response()->json(["message" => "success"]);
    }

    public function get_public_key()
    {
        return response()->json([
            'key' => config('jwt.public_key'),
        ]);
    }

    public function all()
    {
        return User::all();
    }
}
