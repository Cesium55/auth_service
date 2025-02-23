<?php

namespace App\Http\Controllers;

use App\Models\RefreshToken;
use App\Services\JWTManager;
use App\Services\RefreshTokenManager;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Queue\Jobs\Job;
use App\Models\User;

class UserController extends Controller
{
    function register(Request $request)
    {
        $validated = $request->validate([
            "email" => "required|email",
            "password" => "required|min:8"
        ]);

        $user = User::where("email", $validated["email"])->first();

        if ($user != null) {
            return response()->json(["message" => "This email is already registered."], 409);
        }

        $user = User::create([
            "email" => $validated["email"],
            "hashed_password" => Hash::make($validated["password"])
        ]);


        return $user;
    }

    function login(Request $request)
    {
        $validated = $request->validate([
            "email" => "required|email",
            "password" => "required|min:8"
        ]);

        $user = User::where("email", $validated["email"])->first();
        if ($user == null || !Hash::check($validated["password"], $user->hashed_password)){
            return response()->json(["message" => "Unauthorized"], 401);
        }

        $jwt_token = JWTManager::generateToken($user);
        $refresh_token = RefreshTokenManager::generateToken($user->id);

        return response()->json([
            "access_token" => $jwt_token,
            "refresh_token" => $refresh_token->token
        ]);
    }


    function auth(Request $request){
        $validated = $request->validate([
            "access_token" => "required|string|max:1000"
        ]);

        $u = JWTManager::verifyToken($validated["access_token"]);
        if(!$u){
            return response()->json(["message" => "Unauthorized"], 401);
        }

        return response()->json([
            "user" => $u,
            "time" => time()
        ]);

    }

    function refresh(Request $request){
        $validated = $request->validate([
            "refresh_token" => "required|string|max:100"
        ]);
        $token = RefreshTokenManager::verifyToken($validated["refresh_token"]);

        if (!$token){
            return response()->json(["message" => "Unauthorized"], 401);
        }

        $user = User::find($token->user_id)->first();
        if (!$user){
            return response()->json(["message" => "Server error"], status: 500);
        }

        return JWTManager::generateToken($user);

    }

    function all()
    {
        return User::all();
    }
}
