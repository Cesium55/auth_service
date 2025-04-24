<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
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
        return $userService->login(
            $request['email'] ?? '',
            $request['password'] ?? ''
        );
    }

    public function auth(Request $request, UserService $userService)
    {
        return $userService->auth($request->bearerToken() ?? '');
    }

    public function refresh(Request $request, UserService $userService)
    {
        return $userService->refresh_access_token($request['refresh_token'] ?? '');

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
