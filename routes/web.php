<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::group(['middleware' => ['guest']], function () {
    Route::get('/register', [AuthController::class, 'registerView'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'loginView'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::group(['middleware' => ['auth']], function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::group(['prefix' => '/checkout'], function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('checkout');
        Route::post('/', [CheckoutController::class, 'store']);
    });
    Route::get('/buynow/{product}', [CheckoutController::class, 'buynow'])->name('buynow');
    Route::get('/orderdetail/{order}', [CheckoutController::class, 'orderDetail'])->name('orderdetail');
});

Route::group(['prefix' => '/cart'], function () {
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::get('/{product}', [CartController::class, 'addToCart'])->name('cart.add');
    Route::get('/increment/{product}', [CartController::class, 'increment'])->name('cart.inc');
    Route::get('/decrease/{product}', [CartController::class, 'decrease'])->name('cart.dec');
});
