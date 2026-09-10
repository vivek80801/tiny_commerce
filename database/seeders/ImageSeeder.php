<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();
        $categories = Category::all();

        foreach($products as $product)
        {
            Image::factory()
                ->for($product, "imageable")
                ->create();
        }

        foreach($categories as $category)
        {
            Image::factory()
                ->for($category, "imageable")
                ->create();
        }
    }
}
