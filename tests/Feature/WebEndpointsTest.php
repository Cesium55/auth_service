<?php

use App\Models\User;
use Illuminate\Support\Facades\Cookie;

describe('Web Authentication - Happy Path', function () {
    beforeEach(function () {
        User::truncate();
    });

    it('can register a new user', function () {
        $response = $this->postJson('/api/v1/web/register', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(201);
        expect(User::where('email', 'test@example.com')->exists())->toBeTrue();
    });

    it('can login with registered user', function () {
        // Сначала регистрируем пользователя
        $this->postJson('/api/v1/web/register', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        // Затем пробуем войти
        $response = $this->postJson('/api/v1/web/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'success']);

        // Проверяем, что куки были добавлены в очередь
        $this->assertNotEmpty(Cookie::getQueuedCookies());
        $this->assertNotNull(Cookie::getQueuedCookies()[0]->getName() === 'access_token');
        $this->assertNotNull(Cookie::getQueuedCookies()[1]->getName() === 'refresh_token');
    });


});
