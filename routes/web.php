<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::group([
    'middleware' => ['guest'],
    'controller' => AuthController::class,
], function () {
    Route::get('/register', 'registerView')->name('register');
    Route::post('/register', 'register');
    Route::get('/login', 'loginView')->name('login');
    Route::post('/login', 'login');
});

Route::group(['middleware' => ['auth']], function () {

    Route::group([
        'controller' => AuthController::class,
    ], function () {
        Route::post('/logout', 'logout')->name('logout');
        Route::get('/dashboard', 'dashboard')->name('dashboard');
    });

    Route::get('/category', [CategoryController::class, 'index'])
        ->name('category');

    Route::group([
        'controller' => CheckoutController::class,
    ], function () {
        Route::get('/buynow/{product}', 'buynow')
            ->name('buynow');
        Route::get('/orderdetail/{order}', 'orderDetail')
            ->name('orderdetail');
    });

    Route::group([
        'prefix' => '/checkout',
        'controller' => CheckoutController::class,
    ], function () {
        Route::get('/', 'index')->name('checkout');
        Route::post('/', 'store');
    });

    Route::group([
        'prefix' => '/address',
        'controller' => AddressController::class,
    ], function () {
        Route::get('/', 'index')->name('address.index');
        Route::get('/create', 'create')
            ->name('address.create');
        Route::post('/', 'store')
            ->name('address.store');
    });
});

Route::group([
    'prefix' => '/cart',
    'controller' => CartController::class,
], function () {
    Route::get('/', 'index')->name('cart.index');
    Route::get('/{product}', 'addToCart')
        ->name('cart.add');
    Route::get('/increment/{product}', 'increment')
        ->name('cart.inc');
    Route::get('/decrement/{product}', 'decrement')
        ->name('cart.dec');
});
