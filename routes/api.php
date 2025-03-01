<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

    Route::post("/register", [UserController::class, "register"]);
    Route::post("/login", [UserController::class, "login"]);
    Route::post("/auth", [UserController::class, "auth"]);
    Route::post("/refresh", [UserController::class, "refresh"]);

    Route::get("/public-key", [UserController::class, "get_public_key"]);
});
