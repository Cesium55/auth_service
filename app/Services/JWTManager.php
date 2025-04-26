<?php

namespace App\Services;

use App\Models\ApiClient;
use App\Models\User;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTManager
{
    public const ALG = 'RS256';

    public static function generateToken(User $user)
    {
        $privateKey = config('jwt.private_key');
        $expiresIn = config('auth.access_token_lifetime');

        if (! $privateKey) {
            // dd(config('jwt.private_key'), env('JWT_PRIVATE_KEY_PATH'), file_exists(env('JWT_PRIVATE_KEY_PATH')));

            throw new Exception('Private key not found!');
        }

        $payload = [
            'type' => 'user',
            'id' => $user->id,
            'is_admin' => $user->is_admin,
            'confirmed' => $user->is_confirmed,
            'exp' => time() + $expiresIn,
        ];

        return JWT::encode($payload, $privateKey, self::ALG);
    }

    public static function verifyToken($token)
    {
        try {
            $publicKey = config('jwt.public_key');

            if (! $publicKey) {
                throw new Exception('Public key not found!');
            }

            return JWT::decode($token, new Key($publicKey, self::ALG));
        } catch (Exception $e) {
            return null;
        }
    }

    public static function generateApiToken(ApiClient $client)
    {
        $privateKey = config('jwt.private_key');
        $expiresIn = config('auth.api_token_lifetime');

        if (! $privateKey) {
            // dd(config('jwt.private_key'), env('JWT_PRIVATE_KEY_PATH'), file_exists(env('JWT_PRIVATE_KEY_PATH')));

            throw new Exception('Private key not found!');
        }

        $payload = [
            'type' => 'api_client',
            'id' => $client->name,
            'is_admin' => $client->is_admin,
            'exp' => time() + $expiresIn,
        ];

        return JWT::encode($payload, $privateKey, self::ALG);
    }
}
