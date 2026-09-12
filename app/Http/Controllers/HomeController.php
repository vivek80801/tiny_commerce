<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $products = Product::paginate(10);

        return view('welcome', compact('products'));
    }
}
