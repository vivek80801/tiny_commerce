<?php

namespace Tests\Unit;

use App\Models\Order;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\TestCase;

class OrderTest extends TestCase
{
    public function test_order_item_relationship(): void
    {
        $order = new Order;
        $orderItem = $order->orderItem();

        $this->assertInstanceOf(
            HasMany::class,
            $orderItem
        );
    }

    public function test_user_relationship(): void
    {
        $order = new Order;
        $user = $order->user();

        $this->assertInstanceOf(
            BelongsTo::class,
            $user
        );
    }

    public function test_address_relationship(): void
    {
        $order = new Order;
        $address = $order->address();

        $this->assertInstanceOf(
            BelongsTo::class,
            $address
        );
    }
}
