<?php

namespace App\Services;

use App\Models\RefreshToken;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RefreshTokenManager
{
    public static function generateToken(int $userId): RefreshToken
    {
        $token = Str::random(64);
        $version = 1;

        return RefreshToken::create([
            'user_id' => $userId,
            'token' => $token,
            'version' => $version,
        ]);
    }

    public static function verifyToken(string $token): ?RefreshToken
    {
        $refreshToken = RefreshToken::where('token', $token)->first();

        if (!$refreshToken) {
            return null;
        }

        $lifetime = config('auth.refresh_token_lifetime');
        $expiresAt = Carbon::parse($refreshToken->created_at)->addSeconds($lifetime);

        if (now()->greaterThan($expiresAt)) {
            $refreshToken->delete();
            return null;
        }

        return $refreshToken;
    }

    public static function revoke(string $token): void
    {
        RefreshToken::where('token', $token)->delete();
    }
}
