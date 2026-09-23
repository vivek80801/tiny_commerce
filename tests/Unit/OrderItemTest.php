<?php

namespace Tests\Unit;

use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\TestCase;

class OrderItemTest extends TestCase
{
    public function test_order_relationship(): void
    {
        $orderItem = new OrderItem;
        $order = $orderItem->order();

        $this->assertInstanceOf(
            BelongsTo::class,
            $order,
        );
    }

    public function test_product_relationship(): void
    {
        $orderItem = new OrderItem;
        $product = $orderItem->product();

        $this->assertInstanceOf(
            BelongsTo::class,
            $product,
        );
    }
}
