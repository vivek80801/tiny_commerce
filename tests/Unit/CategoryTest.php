<?php

namespace Tests\Unit;

use App\Models\Category;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Foundation\Testing\TestCase;

class CategoryTest extends TestCase
{

    public function test_products_relationship(): void
    {
        $category = new Category;
        $products = $category->products();

        $this->assertInstanceOf(
            HasMany::class,
            $products,
        );
    }

    public function test_image_relationship(): void
    {
        $category = new Category;
        $image = $category->image();

        $this->assertInstanceOf(
            MorphOne::class,
            $image,
        );
    }
}
