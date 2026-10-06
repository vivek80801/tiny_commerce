<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

class GenerateBulkOrderInvoice implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(

    )
    {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $batch = Bus::batch([])
            ->dispatch();

        Order::chunkById(10, function($orders) use ($batch){
            $jobs = $orders->map(function(Order $order){
                $orderItems = OrderItem::where(
                    "order_id",
                    $order->id
                )
                    ->get();

                return [
                    new GenerateOrderInvoce(
                        $orderItems,
                        $order,
                        $order->user_id,
                    )
                ];
            });

            $batch->add($jobs);
        });
    }

    public function failed(\Throwable $e)
    {
        Log::error($e->getMessage());
    }
}
