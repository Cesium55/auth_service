<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiTokensController;

Route::prefix('v1')->group(function () {

    Route::prefix("test")->group(function () {
        Route::get("/all", [UserController::class, "all"]);
        Route::get("/check-auth", function () {
            return "You are authorized!";
        })->middleware(App\Http\Middleware\CheckAuth::class);
    });

    Route::get("/", function () {
        return "Auth api v1";
    });

    Route::post("/user/register", [UserController::class, "register"]);
    Route::post("/user/login", [UserController::class, "login"]);
    Route::post("/user/auth", [UserController::class, "auth"]);
    Route::post("/user/refresh", [UserController::class, "refresh"]);

    Route::get("/public-key", [UserController::class, "get_public_key"]);


    Route::post("/api/login", [ApiTokensController::class, "login"]);
    Route::post("/api/auth", [ApiTokensController::class, "auth"]);


    Route::post("/auth", [ApiTokensController::class, "auth"]);
});
