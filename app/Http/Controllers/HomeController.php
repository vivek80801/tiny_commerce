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

        if (
            request()->query('min_price') ||
            request()->query('max_price')
        ) {
            $minPrice = request()->query('min_price') ?? 0;
            $maxPrice = request()->query('max_price') ?? 0;

            $query = Product::where([
                ['price', '>=', (int) $minPrice * 100],
                ['price', '<=', (int) $maxPrice * 100],
            ]);
        }

        $products = $query
            ->select(
                'id',
                'name',
                'price',
                'quantity',
                'description',
                'category_id',
            )
            ->with('category:id,name')
            ->with('image:id,filename,imageable_id')
            ->paginate(10)
            ->withQueryString();

        request()->flash();

        return view('welcome', compact('products'));
    }
}
