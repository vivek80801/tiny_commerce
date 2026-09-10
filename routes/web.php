<?php

use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $products = Product::paginate(10);

    return view('welcome', compact('products'));
});
