<?php

use App\Models\ApiClient;
use App\Models\User;
use App\Services\JWTManager;
use Firebase\JWT\JWT;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('test valid user token', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    $now = time();
    $token_ttl = config('auth.access_token_lifetime');
    $token = JWTManager::generateToken($user);

    $decoded = JWTManager::verifyToken($token);

    expect($token)->toBeString()->toMatch('/^[A-Za-z0-9_-]+\.[A-Za-z0-9_-]+\.[A-Za-z0-9_-]+$/');
    expect($decoded)->toBeObject();

    expect($decoded->type)->toBe('user');
    expect($decoded->id)->toBe(1);
    expect($decoded->is_admin)->toBe(false);
    expect($decoded->exp)->toBeInt()->toBeGreaterThan($now + $token_ttl - 10)->toBeLessThan($now + $token_ttl + 10);

});

it('test valid admin token', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'is_admin' => true,
    ]);

    $now = time();
    $token_ttl = config('auth.access_token_lifetime');
    $token = JWTManager::generateToken($user);

    $decoded = JWTManager::verifyToken($token);

    expect($token)->toBeString()->toMatch('/^[A-Za-z0-9_-]+\.[A-Za-z0-9_-]+\.[A-Za-z0-9_-]+$/');
    expect($decoded)->toBeObject();

    expect($decoded->type)->toBe('user');
    expect($decoded->id)->toBe(1);
    expect($decoded->is_admin)->toBe(true);
    expect($decoded->exp)->toBeInt()->toBeGreaterThan($now + $token_ttl - 10)->toBeLessThan($now + $token_ttl + 10);

});

it('test 2 valid users tokens', function () {
    $user1 = User::factory()->create([
        'email' => 'test1@example.com',
    ]);
    $user2 = User::factory()->create([
        'email' => 'test2@example.com',
        'is_admin' => true,
    ]);

    $now = time();
    $token_ttl = config('auth.access_token_lifetime');

    $token1 = JWTManager::generateToken($user1);
    $token2 = JWTManager::generateToken($user2);

    $decoded1 = JWTManager::verifyToken($token1);
    $decoded2 = JWTManager::verifyToken($token2);

    expect($token1)->toBeString()->toMatch('/^[A-Za-z0-9_-]+\.[A-Za-z0-9_-]+\.[A-Za-z0-9_-]+$/');
    expect($decoded1)->toBeObject();

    expect($decoded1->type)->toBe('user');
    expect($decoded1->id)->toBe(1);
    expect($decoded1->is_admin)->toBe(false);
    expect($decoded1->exp)->toBeInt()->toBeGreaterThan($now + $token_ttl - 10)->toBeLessThan($now + $token_ttl + 10);

    expect($token2)->toBeString()->toMatch('/^[A-Za-z0-9_-]+\.[A-Za-z0-9_-]+\.[A-Za-z0-9_-]+$/');
    expect($decoded2)->toBeObject();

    expect($decoded2->type)->toBe('user');
    expect($decoded2->id)->toBe(2);
    expect($decoded2->is_admin)->toBe(true);
    expect($decoded2->exp)->toBeInt()->toBeGreaterThan($now + $token_ttl - 10)->toBeLessThan($now + $token_ttl + 10);

});

it('test invalid token', function () {

    $decoded = JWTManager::verifyToken('dfn3u823n8ig2f3uiyn.asdasasddas.34ogynumh234g');

    expect($decoded)->toBeNull();

});

it('test expired user token', function () {

    $privateKey = config('jwt.private_key');
    $expiresIn = -100;

    if (! $privateKey) {
        throw new Exception('Private key not found!');
    }

    $payload = [
        'type' => 'user',
        'id' => 1,
        'is_admin' => false,
        'confirmed' => false,
        'exp' => time() + $expiresIn,
    ];

    $token = JWT::encode($payload, $privateKey, JWTManager::ALG);

    $decoded = JWTManager::verifyToken($token);

    expect($decoded)->toBeNull();

});

it('test valid api token', function () {
    $client = ApiClient::factory()->create(['name' => 'test_api_client']);

    $now = time();
    $token_ttl = config('auth.api_token_lifetime');
    $token = JWTManager::generateApiToken($client);

    $decoded = JWTManager::verifyToken($token);

    expect($token)->toBeString()->toMatch('/^[A-Za-z0-9_-]+\.[A-Za-z0-9_-]+\.[A-Za-z0-9_-]+$/');
    expect($decoded)->toBeObject();

    expect($decoded->type)->toBe('api_client');
    expect($decoded->id)->toBe('test_api_client');
    expect($decoded->is_admin)->toBe(false);
    expect($decoded->exp)->toBeInt()->toBeGreaterThan($now + $token_ttl - 10)->toBeLessThan($now + $token_ttl + 10);

});
