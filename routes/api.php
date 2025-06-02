<?php

use App\Http\Controllers\ApiTokensController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WebAuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::prefix('test')->group(function () {
        Route::get('/all', [UserController::class, 'all']);
        Route::get('/check-auth', function () {
            return 'You are authorized!';
        })->middleware(App\Http\Middleware\CheckAuth::class);
    });

    Route::get('/swagger/swagger.json', function () {
        $path = public_path('swagger/swagger.json');

        if (!file_exists($path)) {
            abort(404);
        }

        return Response::file($path, [
            'Access-Control-Allow-Origin' => '*',
        ]);
    });

    Route::prefix('web')->group(function () {
        Route::post('/register', [WebAuthController::class, 'register']);
        Route::post('/login', [WebAuthController::class, 'login']);
        Route::post('/auth', [WebAuthController::class, 'auth']);
        Route::post('/refresh', [WebAuthController::class, 'refresh']);

    });


    Route::post('/users/register', [UserController::class, 'register']);
    Route::post('/users/login', [UserController::class, 'login']);
    Route::post('/users/auth', [UserController::class, 'auth']);
    Route::post('/users/refresh', [UserController::class, 'refresh']);

    Route::get('/public-key', [UserController::class, 'get_public_key']);

    Route::post('/api/login', [ApiTokensController::class, 'login']);
    Route::post('/api/auth', [ApiTokensController::class, 'auth']);

    Route::post('/auth', [ApiTokensController::class, 'auth']);
});
