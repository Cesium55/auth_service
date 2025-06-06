<?php

namespace App\Services;

use App\Models\User;
use App\Validators\LoginUserValidator;
use App\Validators\RegisterUserValidator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Logger;

class UserService
{
    public function register(string $email, string $password)
    {

        RegisterUserValidator::validate(['email' => $email, 'password' => $password]);

        $user = User::create([
            'email' => $email,
            'hashed_password' => Hash::make($password),
        ]);

        return $user;
    }

    public function login(string $email, string $password)
    {
        LoginUserValidator::validate(['email' => $email, 'password' => $password]);

        $user = User::where('email', $email)->first();
        if (! $user || ! Hash::check($password, $user->hashed_password)) {
            abort(401, 'Unauthorized');
        }

        $jwt_token = JWTManager::generateToken($user);
        $refresh_token = RefreshTokenManager::generateToken($user->id);

        return [
            'access_token' => $jwt_token,
            'refresh_token' => $refresh_token->token,
        ];
    }

    public function auth(string $access_token)
    {
        $u = JWTManager::verifyToken($access_token);
        if (! $u) {
            abort(401, 'Unauthorized');
        }

        return $u;
    }

    public function refresh_access_token(string $refresh_token)
    {
        $token = RefreshTokenManager::verifyToken($refresh_token);

        if (! $token) {
            abort(401, 'Unauthorized');
        }


        logger()->info("token user id " . $token->user_id);

        $user = User::find($token->user_id);
        if (! $user) {
            abort(401, 'Unauthorized');
        }
        logger()->info("user id " . $user->id);

        return [
            'access_token' => JWTManager::generateToken($user),
        ];
    }
}
