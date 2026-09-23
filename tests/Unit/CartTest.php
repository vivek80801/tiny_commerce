<?php

namespace Tests\Unit;

use App\Models\Cart;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\TestCase;

class CartTest extends TestCase
{
    public function test_user_relationship(): void
    {
        $cart = new Cart;
        $user = $cart->user();

        $this->assertInstanceOf(
            BelongsTo::class,
            $user
        );
    }

    public function test_product_relationship(): void
    {
        $cart = new Cart;
        $product = $cart->product();

        $this->assertInstanceOf(
            BelongsTo::class,
            $product,
        );
    }
}
