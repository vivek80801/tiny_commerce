<?php

namespace Tests\Unit;

use App\Models\Product;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Foundation\Testing\TestCase;

class ProductTest extends TestCase
{
    public function test_category_relationship(): void
    {
        $product = new Product;
        $category = $product->category();

        $this->assertInstanceOf(
            BelongsTo::class,
            $category,
        );
    }

    public function test_image_relationship(): void
    {
        $product = new Product;
        $image = $product->image();

        $this->assertInstanceOf(
            MorphOne::class,
            $image
        );
    }

    public function test_cart_relationship(): void
    {
        $product = new Product;
        $cart = $product->cart();

        $this->assertInstanceOf(
            HasMany::class,
            $cart,
        );
    }

    public function test_convert_price(): void
    {
        $product = new Product(["price" => 3232]);

        $this->assertEquals(32.32, $product->getPrice());
    }
}
