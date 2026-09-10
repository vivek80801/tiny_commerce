<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, "index"])->name('home');

Route::group(["middleware" => ["guest"]], function(){
    Route::get("/register", [AuthController::class, "registerView"])->name('register');
    Route::post("/register", [AuthController::class, "register"]);
    Route::get("/login", [AuthController::class, "loginView"])->name('login');
    Route::post("/login", [AuthController::class, "login"]);
});

Route::group(["middleware" => ["auth"]], function(){
    Route::post("/logout", [AuthController::class, "logout"])->name("logout");
    Route::get("/dashboard", [AuthController::class, "dashboard"])->name("dashboard");
});
