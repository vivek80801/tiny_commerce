<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

class GenerateInoviceAndSendEmail implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     *
     * @param  Collection<OrderItem>  $orderItems
     */
    public function __construct(
        private Collection $orderItems,
        private Order $order,
        private int $userId,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = User::findOrFail(
            $this->userId
        );

        Bus::chain([
            new GenerateOrderInvoce(
                $this->orderItems,
                $this->order,
                $this->userId,
            ),
            new SendOrderEmailJob(
                $this->order,
                $user,
            ),
        ])->dispatch();

    }

    public function failed(\Throwable $e): void
    {
        Log::error($e->getMessage());
    }
}
