<?php

use App\Models\User;
use App\Validators\RegisterUserValidator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

it('testting test', function () {
    $a = 1 + 3;

    expect($a)->toBe(4);
});

it('validates valid data successfully', function () {
    $data = [
        'email' => 'test@example.com',
        'password' => 'strongpassword',
    ];

    $validated = RegisterUserValidator::validate($data);

    expect($validated)->toBeArray();
    expect($validated['email'])->toBe('test@example.com');
    expect($validated['password'])->toBe('strongpassword');
});

it('throws ValidationException when email is invalid', function () {
    $data = [
        'email' => 'invalid-email',
        'password' => 'strongpassword',
    ];

    expect(fn () => RegisterUserValidator::validate($data))
        ->toThrow(ValidationException::class);
});

it('throws ValidationException when password is too short', function () {
    $data = [
        'email' => 'test@example.com',
        'password' => 'short',
    ];

    expect(fn () => RegisterUserValidator::validate($data))
        ->toThrow(ValidationException::class);
});

it('throws ValidationException when email is already taken', function () {
    User::factory()->create([
        'email' => 'test@example.com',
    ]);

    $data = [
        'email' => 'test@example.com',
        'password' => 'strongpassword',
    ];

    expect(fn () => RegisterUserValidator::validate($data))
        ->toThrow(ValidationException::class);
});
