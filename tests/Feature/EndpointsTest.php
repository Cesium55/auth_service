<?php

it('get all users', function () {
    $response = $this->get('/api/v1/test/all');
    expect($response->status())->toBe(200);
});

it('register valid user', function () {
    $response = $this->post('/api/v1/users/register', ['email' => 'test@email.dota', 'password' => '12345678'], ['Accept' => 'application/json']);
    expect($response->status())->toBe(201);
});

it('register already registered user', function () {
    $response1 = $this->post('/api/v1/users/register', ['email' => 'test@email.dota', 'password' => '12345678'], ['Accept' => 'application/json']);
    $response2 = $this->post('/api/v1/users/register', ['email' => 'test@email.dota', 'password' => '12345678'], ['Accept' => 'application/json']);
    expect($response1->status())->toBe(201);
    expect($response2->status())->toBe(422);
});

it('test register with bad email', function () {
    $response = $this->post('/api/v1/users/register', ['email' => 'lalalalal', 'password' => '12345678'], ['Accept' => 'application/json']);
    expect($response->status())->toBe(422);
});

it('test register without data', function () {
    $response = $this->post('/api/v1/users/register');
    expect($response->status())->toBe(422);
});

it('test register with bad password', function () {
    $response = $this->post('/api/v1/users/register', ['email' => 'test@email.dota', 'password' => '123']);
    expect($response->status())->toBe(422);
});

it('test valid login', function () {
    $this->post('/api/v1/users/register', ['email' => 'test@email.dota', 'password' => '12345678']);
    $response = $this->post('/api/v1/users/login', ['email' => 'test@email.dota', 'password' => '12345678']);

    expect($response->status())->toBe(200);
    expect($response->getData())
        ->access_token->toBeString()
        ->refresh_token->toBeString();
});

it('test invalid login', function () {
    $this->post('/api/v1/users/register', ['email' => 'test@email.dota', 'password' => '12345678']);
    $response = $this->post('/api/v1/users/login', ['email' => 'test@email.dota', 'password' => '123456278']);

    expect($response->status())->toBe(401);
});

it('test valid auth', function () {
    $this->post('/api/v1/users/register', ['email' => 'test@email.dota', 'password' => '12345678']);
    $response1 = $this->post('/api/v1/users/login', ['email' => 'test@email.dota', 'password' => '12345678']);

    $response2 = $this->post('/api/v1/users/auth', [], ['Authorization' => 'Bearer '.$response1->getData()->access_token]);

    expect($response1->status())->toBe(200);
    expect($response1->getData())
        ->access_token->toBeString()
        ->refresh_token->toBeString();
    expect($response2->status())->toBe(200);
});

it('test invalid auth', function () {
    $this->post('/api/v1/users/register', ['email' => 'test@email.dota', 'password' => '12345678']);
    $response1 = $this->post('/api/v1/users/login', ['email' => 'test@email.dota', 'password' => '12345678']);

    $response2 = $this->post('/api/v1/users/auth', [], ['Authorization' => 'Bearer '.'asdasdhjkasdhjkdashjkdas']);

    expect($response2->status())->toBe(401);
});

it('test expired auth token', function () {

    config(['auth.access_token_lifetime' => 0]);

    $this->post('/api/v1/users/register', ['email' => 'test@email.dota', 'password' => '12345678']);
    $response1 = $this->post('/api/v1/users/login', ['email' => 'test@email.dota', 'password' => '12345678']);

    $response2 = $this->post('/api/v1/users/auth', [], ['Authorization' => 'Bearer '.$response1->getData()->access_token]);

    expect($response2->status())->toBe(401);
});

it('test valid refresh token', function () {

    $this->post('/api/v1/users/register', ['email' => 'test@email.dota', 'password' => '12345678']);
    $response1 = $this->post('/api/v1/users/login', ['email' => 'test@email.dota', 'password' => '12345678']);

    $refresh_token = $response1->getData()->refresh_token;
    $response2 = $this->post('/api/v1/users/refresh', ['refresh_token' => $refresh_token]);

    $access_token = $response2->getData()->access_token;
    $response3 = $response2 = $this->post('/api/v1/users/auth', [], ['Authorization' => 'Bearer '.$access_token]);

    expect($response2->status())->toBe(200);
    expect($access_token)->toBeString();
    expect($refresh_token)->toBeString();
    expect($response3->status())->toBe(200);
});
