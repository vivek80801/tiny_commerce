<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Contracts\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::select(
            'id',
            'name',
        )
            ->with('image:id,filename,imageable_id')
            ->paginate(10);

        return view('user.category', compact('categories'));
    }
}
