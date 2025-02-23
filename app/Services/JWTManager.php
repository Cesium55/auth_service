<?php

namespace App\Services;

use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class JWTManager
{
    public static function generateToken(User $user)
    {
        $privateKey = config('jwt.private_key');
        $expiresIn = config('auth.access_token_lifetime');

        if (!$privateKey) {
            //dd(config('jwt.private_key'), env('JWT_PRIVATE_KEY_PATH'), file_exists(env('JWT_PRIVATE_KEY_PATH')));

            throw new Exception("Private key not found!");
        }

        $payload = [
            "id" => $user->id,
            "is_admin" => $user->is_admin,
            "confirmed" => $user->is_confirmed,
            "exp" => time() + $expiresIn
        ];

        return JWT::encode($payload, $privateKey, 'RS256');
    }

    public static function verifyToken($token)
    {
        try {
            $publicKey = config('jwt.public_key');

            if (!$publicKey) {
                throw new Exception("Public key not found!");
            }

            return JWT::decode($token, new Key($publicKey, 'RS256'), );
        } catch (Exception $e) {
            return null;
        }
    }
}
