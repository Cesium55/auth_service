<?php

use App\Models\User;
use App\Services\RefreshTokenManager;
use Illuminate\Support\Facades\Config;

it('generates valid token', function () {

    $user = User::factory()->create();

    $token = RefreshTokenManager::generateToken(1);

    expect($token)->toBeObject();
    expect($token->user_id)->toBe(1);
    expect($token->version)->toBe(1);
    expect($token->token)->toBeString()->toHaveLength(64);
});

it('throws foreign key error', function () {

    expect(function () {
        $user = User::factory()->create();
        RefreshTokenManager::generateToken($user->id + 1);
    })->toThrow(\Illuminate\Database\QueryException::class);

});

it('verifies valid token', function () {

    $user = User::factory()->create();

    $token = RefreshTokenManager::generateToken($user->id);

    $verified = RefreshTokenManager::verifyToken($token->token);

    expect($verified->user_id)->toBe($token->user_id);
    expect($verified->token)->toBe($token->token);
    expect($verified->version)->toBe($token->version);

});

it('verifies invalid token', function () {

    $verified = RefreshTokenManager::verifyToken('qwerty');

    expect($verified)->toBeNull();

});

it('verifies expired token', function () {

    Config::set('auth.refresh_token_lifetime', 0);

    User::factory()->create();

    $token = RefreshTokenManager::generateToken(1);

    $verified = RefreshTokenManager::verifyToken($token->token);

    expect($verified)->toBeNull();

});
