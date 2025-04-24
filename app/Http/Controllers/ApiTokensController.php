<?php

namespace App\Http\Controllers;

use App\Models\ApiClient;
use App\Services\JWTManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ApiTokensController extends Controller
{
    public static function login(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'client_secret' => 'required',
        ]);

        $client = ApiClient::where('name', $validated['name'])->first();
        if ($client == null || ! Hash::check($validated['client_secret'], $client->client_secret)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $jwt_token = JWTManager::generateApiToken($client);

        return response()->json([
            'api_token' => $jwt_token,
        ]);
    }

    public static function auth(Request $request)
    {
        $token = $request->bearerToken();

        if (! $token) {
            return response()->json(['message' => 'No access token provided'], 401);
        }

        $client = JWTManager::verifyToken($token);
        if (! $client) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $client;
    }
}
