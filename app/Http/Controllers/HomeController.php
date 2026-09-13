<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $query = Product::query();

        if (request()->query('category')) {
            $query = Product::where(
                'category_id',
                request()->query('category')
            );
        }

        if (request()->query('search')) {
            $query = Product::where(
                'name',
                'like',
                '%'.request()->query('search').'%'
            );

            $tmpProduct = $query->first();
            if (! $tmpProduct) {
                $query = Product::whereHas(
                    'category', function ($search) {
                        $search->where(
                            'name',
                            'like',
                            '%'.request()->query('search').'%'
                        );
                    }
                );
            }
        }

        $products = $query->paginate(10);

        request()->flash();

        return view('welcome', compact('products'));
    }
}
