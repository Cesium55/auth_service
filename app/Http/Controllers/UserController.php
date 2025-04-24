<?php

namespace App\Http\Controllers;

use App\Models\RefreshToken;
use App\Services\JWTManager;
use App\Services\RefreshTokenManager;
use App\Services\UserService;
use App\Validators\RegisterUserValidator;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Queue\Jobs\Job;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    function register(Request $request, UserService $userService)
    {
        return $userService->register(
            $request["email"] ?? "",
            $request["password"] ?? ""
        );
    }

    function login(Request $request, UserService $userService)
    {
        return $userService->login(
            $request["email"] ?? "",
            $request["password"] ?? ""
        );
    }


    function auth(Request $request, UserService $userService)
    {
        return $userService->auth($request->bearerToken() ?? "");
    }

    function refresh(Request $request, UserService $userService)
    {
        return $userService->refresh_access_token($request["refresh_token"] ?? "");

    }


    function get_public_key()
    {
        return response()->json([
            "key" => config("jwt.public_key")
        ]);
    }

    function all()
    {
        return User::all();
    }


}
