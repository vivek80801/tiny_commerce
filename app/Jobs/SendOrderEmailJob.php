<?php

namespace App\Jobs;

use App\Mail\SendOrderEmail;
use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendOrderEmailJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private Order $order,
        private User $user,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // I know this is not Idempotent.
        // I have no intention to add it.
        Mail::to(
            $this->user
        )
            ->send(
                new SendOrderEmail(
                    $this->order
                ),
            );

    }
}
