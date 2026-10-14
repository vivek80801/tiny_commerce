<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;

class OrderItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        OrderItem::factory(6)
            ->create();

        $orders = Order::all();

        foreach ($orders as $order) {
            $total = 0;
            $orderItems = OrderItem::where(
                'order_id',
                $order->id
            )->get();

            if (count($orderItems) > 0) {
                foreach ($orderItems as $orderItem) {
                    $total += $orderItem->amount;
                }
            }

            $order->total = $total;
            $order->save();
        }
    }
}
